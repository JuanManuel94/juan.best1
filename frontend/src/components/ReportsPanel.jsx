import React, { useState, useEffect } from 'react';
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import * as XLSX from 'xlsx';
import { saveAs } from 'file-saver';
import './ReportsPanel.css';

const ReportsPanel = () => {
    const [reportType, setReportType] = useState('vehicles'); // vehicles, alerts, history
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [selectedVehicle, setSelectedVehicle] = useState('');
    const [vehicles, setVehicles] = useState([]);
    const [reportData, setReportData] = useState([]);
    const [loading, setLoading] = useState(false);
    const [searched, setSearched] = useState(false);

    // Fetch vehicles list for filter
    useEffect(() => {
        fetch('http://localhost:3000/api/vehicles')
            .then(res => res.json())
            .then(data => setVehicles(data))
            .catch(err => console.error('Error fetching vehicles:', err));
    }, []);

    // Set default dates (last 7 days)
    useEffect(() => {
        const end = new Date();
        const start = new Date();
        start.setDate(start.getDate() - 7);

        setEndDate(end.toISOString().split('T')[0]);
        setStartDate(start.toISOString().split('T')[0]);
    }, []);

    const fetchReport = async () => {
        setLoading(true);
        setSearched(true);

        try {
            let url = `http://localhost:3000/api/reports/${reportType}?`;
            const params = new URLSearchParams();

            if (startDate) params.append('startDate', startDate);
            if (endDate) params.append('endDate', endDate + 'T23:59:59');
            if (selectedVehicle) params.append('vehicleId', selectedVehicle);

            url += params.toString();

            const res = await fetch(url);
            const data = await res.json();
            setReportData(data);
        } catch (error) {
            console.error('Error fetching report:', error);
            setReportData([]);
        } finally {
            setLoading(false);
        }
    };

    const exportToPDF = () => {
        const doc = new jsPDF();

        // Header
        doc.setFontSize(20);
        doc.setTextColor(30, 41, 59);
        doc.text('XY Transporte S.A.', 14, 20);

        doc.setFontSize(14);
        doc.setTextColor(100, 116, 139);
        doc.text(`Reporte de ${getReportTitle()}`, 14, 30);

        doc.setFontSize(10);
        doc.text(`Período: ${startDate || 'Inicio'} - ${endDate || 'Fin'}`, 14, 38);
        doc.text(`Generado: ${new Date().toLocaleString()}`, 14, 44);

        // Table
        const headers = getTableHeaders();
        const rows = reportData.map(item => getTableRow(item));

        autoTable(doc, {
            head: [headers],
            body: rows,
            startY: 52,
            styles: { fontSize: 9 },
            headStyles: {
                fillColor: [59, 130, 246],
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: { fillColor: [248, 250, 252] }
        });

        doc.save(`reporte_${reportType}_${new Date().toISOString().split('T')[0]}.pdf`);
    };

    const exportToExcel = () => {
        const headers = getTableHeaders();
        const rows = reportData.map(item => getTableRow(item));

        const wsData = [headers, ...rows];
        const ws = XLSX.utils.aoa_to_sheet(wsData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Reporte');

        const excelBuffer = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
        const data = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        saveAs(data, `reporte_${reportType}_${new Date().toISOString().split('T')[0]}.xlsx`);
    };

    const getReportTitle = () => {
        switch (reportType) {
            case 'vehicles': return 'Vehículos';
            case 'alerts': return 'Alertas';
            case 'history': return 'Historial de Ubicaciones';
            default: return '';
        }
    };

    const getTableHeaders = () => {
        switch (reportType) {
            case 'vehicles':
                return ['Nombre', 'Ciudad', 'Departamento', 'Última Actualización', 'Total Ubicaciones', 'Primer Registro', 'Último Registro'];
            case 'alerts':
                return ['Vehículo', 'Tipo', 'Mensaje', 'Fecha/Hora', 'Ciudad'];
            case 'history':
                return ['Vehículo', 'Latitud', 'Longitud', 'Fecha/Hora'];
            default:
                return [];
        }
    };

    const getTableRow = (item) => {
        switch (reportType) {
            case 'vehicles':
                return [
                    item.name,
                    item.city || '-',
                    item.department || '-',
                    item.lastUpdate ? new Date(item.lastUpdate).toLocaleString() : '-',
                    item.totalLocations || 0,
                    item.firstRecord ? new Date(item.firstRecord).toLocaleString() : '-',
                    item.lastRecord ? new Date(item.lastRecord).toLocaleString() : '-'
                ];
            case 'alerts':
                return [
                    item.vehicleName || item.vehicle_id,
                    item.type || '-',
                    item.message || '-',
                    item.timestamp ? new Date(item.timestamp).toLocaleString() : '-',
                    item.city || '-'
                ];
            case 'history':
                return [
                    item.vehicleName || item.vehicle_id,
                    item.lat?.toFixed(6) || '-',
                    item.lng?.toFixed(6) || '-',
                    item.timestamp ? new Date(item.timestamp).toLocaleString() : '-'
                ];
            default:
                return [];
        }
    };

    return (
        <div className="reports-panel">
            <h3>📊 Generador de Reportes</h3>

            {/* Report Type Tabs */}
            <div className="report-type-tabs">
                <button
                    className={`report-type-tab ${reportType === 'vehicles' ? 'active' : ''}`}
                    onClick={() => { setReportType('vehicles'); setReportData([]); setSearched(false); }}
                >
                    🚗 Vehículos
                </button>
                <button
                    className={`report-type-tab ${reportType === 'alerts' ? 'active' : ''}`}
                    onClick={() => { setReportType('alerts'); setReportData([]); setSearched(false); }}
                >
                    🔔 Alertas
                </button>
                <button
                    className={`report-type-tab ${reportType === 'history' ? 'active' : ''}`}
                    onClick={() => { setReportType('history'); setReportData([]); setSearched(false); }}
                >
                    📍 Historial
                </button>
            </div>

            {/* Filters */}
            <div className="reports-filters">
                <div className="report-filter-group">
                    <label>📅 Fecha Inicio</label>
                    <input
                        type="date"
                        value={startDate}
                        onChange={(e) => setStartDate(e.target.value)}
                    />
                </div>
                <div className="report-filter-group">
                    <label>📅 Fecha Fin</label>
                    <input
                        type="date"
                        value={endDate}
                        onChange={(e) => setEndDate(e.target.value)}
                    />
                </div>
                <div className="report-filter-group">
                    <label>🚗 Vehículo</label>
                    <select
                        value={selectedVehicle}
                        onChange={(e) => setSelectedVehicle(e.target.value)}
                    >
                        <option value="">Todos los vehículos</option>
                        {vehicles.map(v => (
                            <option key={v.id} value={v.id}>{v.name}</option>
                        ))}
                    </select>
                </div>
                <div className="report-actions">
                    <button
                        className="report-btn search"
                        onClick={fetchReport}
                        disabled={loading || (reportType === 'history' && !selectedVehicle)}
                    >
                        {loading ? '⏳ Buscando...' : '🔍 Buscar'}
                    </button>
                    <button
                        className="report-btn pdf"
                        onClick={exportToPDF}
                        disabled={reportData.length === 0}
                    >
                        📄 PDF
                    </button>
                    <button
                        className="report-btn excel"
                        onClick={exportToExcel}
                        disabled={reportData.length === 0}
                    >
                        📊 Excel
                    </button>
                </div>
            </div>

            {reportType === 'history' && !selectedVehicle && (
                <div className="report-empty" style={{ color: '#f59e0b' }}>
                    ⚠️ Selecciona un vehículo para ver su historial
                </div>
            )}

            {/* Results */}
            {loading && (
                <div className="report-loading">
                    <div className="spinner"></div>
                    <span>Generando reporte...</span>
                </div>
            )}

            {!loading && searched && reportData.length === 0 && (
                <div className="report-empty">
                    📭 No se encontraron datos para los filtros seleccionados
                </div>
            )}

            {!loading && reportData.length > 0 && (
                <div className="report-results">
                    <div className="report-summary">
                        <div className="summary-card">
                            <div className="value">{reportData.length}</div>
                            <div className="label">Registros</div>
                        </div>
                        {reportType === 'vehicles' && (
                            <div className="summary-card">
                                <div className="value">
                                    {reportData.reduce((sum, v) => sum + (v.totalLocations || 0), 0)}
                                </div>
                                <div className="label">Total Ubicaciones</div>
                            </div>
                        )}
                    </div>

                    <h4>📋 Resultados ({reportData.length})</h4>
                    <div style={{ overflowX: 'auto' }}>
                        <table className="report-table">
                            <thead>
                                <tr>
                                    {getTableHeaders().map((header, i) => (
                                        <th key={i}>{header}</th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {reportData.slice(0, 100).map((item, i) => (
                                    <tr key={i}>
                                        {getTableRow(item).map((cell, j) => (
                                            <td key={j}>{cell}</td>
                                        ))}
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {reportData.length > 100 && (
                        <p style={{ color: '#64748b', fontSize: '14px', marginTop: '12px' }}>
                            Mostrando 100 de {reportData.length} registros. Exporta a PDF/Excel para ver todos.
                        </p>
                    )}
                </div>
            )}
        </div>
    );
};

export default ReportsPanel;
