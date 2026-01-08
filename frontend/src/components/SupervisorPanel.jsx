import React, { useState, useEffect } from 'react';
import MapComponent from './MapComponent';
import { COLOMBIA_CITIES, DEPARTMENT_NAMES, getMunicipios, getMunicipioData } from '../constants/cities';
import './SupervisorPanel.css';

const SupervisorPanel = ({ socket, user, onLogout }) => {
    const [vehicles, setVehicles] = useState([]);

    // Get allowed departments for this supervisor (from assignedCities which now stores departments)
    // Handle case where assignedCities might be a JSON string
    const getAllowedDepartments = () => {
        if (!user?.assignedCities) return [];
        if (Array.isArray(user.assignedCities)) return user.assignedCities;
        // Try parsing if it's a string
        try {
            const parsed = JSON.parse(user.assignedCities);
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    };

    const allowedDepartments = getAllowedDepartments();

    // Set initial department to first allowed or empty
    const [selectedDepartment, setSelectedDepartment] = useState('');
    const [vehicleForm, setVehicleForm] = useState({
        name: '',
        lat: '',
        lng: '',
        city: '',
        department: ''
    });
    const [editingVehicle, setEditingVehicle] = useState(null);
    const [message, setMessage] = useState({ text: '', type: '' });
    const [focusedVehicle, setFocusedVehicle] = useState(null);

    // Update selected department when user changes
    useEffect(() => {
        if (allowedDepartments.length > 0 && !selectedDepartment) {
            setSelectedDepartment(allowedDepartments[0]);
            setVehicleForm(prev => ({ ...prev, department: allowedDepartments[0] }));
        }
    }, [allowedDepartments]);

    useEffect(() => {
        fetchVehicles();
    }, [allowedDepartments]);

    const fetchVehicles = async () => {
        try {
            const res = await fetch('http://localhost:3000/api/vehicles');
            const data = await res.json();

            // Filter vehicles based on assigned DEPARTMENTS (not cities)
            let filteredData = data;
            if (allowedDepartments.length > 0) {
                // Filter vehicles where department matches one of the allowed departments
                filteredData = data.filter(v => allowedDepartments.includes(v.department));
            }

            setVehicles(filteredData);
        } catch (error) {
            console.error('Error fetching vehicles:', error);
        }
    };

    const handleVehicleChange = (e) => {
        setVehicleForm({ ...vehicleForm, [e.target.name]: e.target.value });
    };

    // Handle department change for vehicle form
    const handleDepartmentChange = (e) => {
        const deptName = e.target.value;
        setSelectedDepartment(deptName);
        setVehicleForm({
            ...vehicleForm,
            department: deptName,
            city: '',
            lat: '',
            lng: ''
        });
    };

    // Handle municipality selection for vehicle form
    const handleMunicipioSelect = (e) => {
        const municipioName = e.target.value;
        const municipioData = getMunicipioData(selectedDepartment, municipioName);

        if (municipioData) {
            setVehicleForm({
                ...vehicleForm,
                city: municipioData.name,
                department: selectedDepartment,
                lat: municipioData.lat.toFixed(6),
                lng: municipioData.lng.toFixed(6)
            });
            setFocusedVehicle({ lat: municipioData.lat, lng: municipioData.lng, type: 'city_center' });
        } else {
            setVehicleForm({ ...vehicleForm, city: '' });
        }
    };

    const handleMapClick = (latlng) => {
        const { lat, lng } = latlng;

        if (editingVehicle) {
            setEditingVehicle(prev => ({
                ...prev,
                lat: lat.toFixed(6),
                lng: lng.toFixed(6)
            }));
            setMessage({ text: '📍 Ubicación del vehículo actualizada en el mapa', type: 'success' });
            setTimeout(() => setMessage({ text: '', type: '' }), 2000);
        } else {
            setVehicleForm(prev => ({
                ...prev,
                lat: lat.toFixed(6),
                lng: lng.toFixed(6)
            }));
            setMessage({ text: '📍 Ubicación seleccionada. Recuerda seleccionar Departamento y Municipio.', type: 'success' });
            setTimeout(() => setMessage({ text: '', type: '' }), 3000);
        }
    };

    const handleGetCurrentLocation = () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    setVehicleForm({
                        ...vehicleForm,
                        lat: position.coords.latitude.toFixed(6),
                        lng: position.coords.longitude.toFixed(6)
                    });
                    setMessage({ text: 'Ubicación obtenida exitosamente', type: 'success' });
                    setTimeout(() => setMessage({ text: '', type: '' }), 3000);
                },
                (error) => {
                    setMessage({ text: 'Error obteniendo ubicación', type: 'error' });
                    setTimeout(() => setMessage({ text: '', type: '' }), 3000);
                }
            );
        }
    };

    const handleAddVehicle = async (e) => {
        e.preventDefault();
        if (!vehicleForm.name.trim()) {
            setMessage({ text: 'El nombre del vehículo es requerido', type: 'error' });
            return;
        }

        const lat = parseFloat(vehicleForm.lat);
        const lng = parseFloat(vehicleForm.lng);

        if (isNaN(lat) || isNaN(lng)) {
            setMessage({ text: 'Coordenadas inválidas', type: 'error' });
            return;
        }

        try {
            const res = await fetch('http://localhost:3000/api/vehicles', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ...vehicleForm, lat, lng }),
            });

            const data = await res.json();

            // Verificar si el servidor respondió con éxito
            if (!res.ok) {
                setMessage({ text: data.error || 'Error al agregar vehículo', type: 'error' });
                setTimeout(() => setMessage({ text: '', type: '' }), 4000);
                return;
            }

            setVehicles([...vehicles, data]);
            const firstDept = allowedDepartments.length > 0 ? allowedDepartments[0] : '';
            setVehicleForm({ name: '', lat: '', lng: '', city: '', department: firstDept });
            setSelectedDepartment(firstDept);
            setMessage({ text: 'Vehículo agregado exitosamente', type: 'success' });
            setTimeout(() => setMessage({ text: '', type: '' }), 3000);
        } catch (error) {
            console.error('Error adding vehicle:', error);
            setMessage({ text: 'Error de conexión con el servidor', type: 'error' });
            setTimeout(() => setMessage({ text: '', type: '' }), 4000);
        }
    };

    const startEditVehicle = (vehicle) => {
        setEditingVehicle({
            ...vehicle,
            department: vehicle.department || '',
            city: vehicle.city || '',
            lat: vehicle.lat || '',
            lng: vehicle.lng || ''
        });
    };

    const cancelEditVehicle = () => {
        setEditingVehicle(null);
    };

    const handleEditVehicleChange = (e) => {
        const { name, value } = e.target;

        if (name === 'department') {
            // When department changes, clear city and coordinates
            setEditingVehicle(prev => ({
                ...prev,
                department: value,
                city: '',
                lat: '',
                lng: ''
            }));
        } else if (name === 'city' && editingVehicle.department) {
            // When municipality is selected, update coordinates
            const municipioData = getMunicipioData(editingVehicle.department, value);
            if (municipioData) {
                setEditingVehicle(prev => ({
                    ...prev,
                    city: value,
                    lat: municipioData.lat.toFixed(6),
                    lng: municipioData.lng.toFixed(6)
                }));
                // Focus map on new location
                setFocusedVehicle({ lat: municipioData.lat, lng: municipioData.lng, type: 'city_center' });
            } else {
                setEditingVehicle(prev => ({ ...prev, city: value }));
            }
        } else {
            setEditingVehicle(prev => ({ ...prev, [name]: value }));
        }
    };

    const handleUpdateVehicle = async (id) => {
        if (!editingVehicle.name.trim()) {
            setMessage({ text: 'El nombre es requerido', type: 'error' });
            return;
        }

        if (!editingVehicle.lat || !editingVehicle.lng) {
            setMessage({ text: 'Las coordenadas son requeridas', type: 'error' });
            return;
        }

        try {
            const res = await fetch(`http://localhost:3000/api/vehicles/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: editingVehicle.name,
                    lat: parseFloat(editingVehicle.lat),
                    lng: parseFloat(editingVehicle.lng),
                    city: editingVehicle.city,
                    department: editingVehicle.department
                }),
            });
            const data = await res.json();

            // Verificar si el servidor respondió con éxito
            if (!res.ok) {
                setMessage({ text: data.error || 'Error al actualizar vehículo', type: 'error' });
                setTimeout(() => setMessage({ text: '', type: '' }), 4000);
                return;
            }

            setVehicles(vehicles.map(v => (v.id === id ? data : v)));
            setEditingVehicle(null);
            setMessage({ text: 'Vehículo actualizado exitosamente', type: 'success' });
            setTimeout(() => setMessage({ text: '', type: '' }), 3000);
        } catch (error) {
            console.error('Error updating vehicle:', error);
            setMessage({ text: 'Error de conexión con el servidor', type: 'error' });
            setTimeout(() => setMessage({ text: '', type: '' }), 4000);
        }
    };

    const handleFollowVehicle = (vehicle) => {
        setFocusedVehicle(vehicle);
        // Scroll to map
        document.querySelector('.map-section').scrollIntoView({ behavior: 'smooth' });
    };

    const handleDeleteVehicle = async (id) => {
        if (!window.confirm('¿Estás seguro de que deseas eliminar este vehículo?')) {
            return;
        }

        try {
            const res = await fetch(`http://localhost:3000/api/vehicles/${id}`, {
                method: 'DELETE',
            });

            if (res.ok) {
                setVehicles(vehicles.filter(v => v.id !== id));
                setMessage({ text: 'Vehículo eliminado exitosamente', type: 'success' });
                setTimeout(() => setMessage({ text: '', type: '' }), 3000);
            } else {
                setMessage({ text: 'Error al eliminar vehículo', type: 'error' });
            }
        } catch (error) {
            console.error('Error deleting vehicle:', error);
            setMessage({ text: 'Error al eliminar vehículo', type: 'error' });
        }
    };

    return (
        <div className="supervisor-panel">
            <div className="panel-header">
                <div className="header-left">
                    <h2>🚗 Panel de Supervisión</h2>
                    <p className="supervisor-name">Supervisor: <strong>{user?.name}</strong></p>
                </div>
                <button onClick={onLogout} className="logout-btn-header">
                    🚪 Cerrar Sesión
                </button>
            </div>

            <div className="panel-content">
                {/* Info about assigned departments */}
                {allowedDepartments.length > 0 ? (
                    <div className="access-info" style={{ background: 'var(--success-bg)', padding: '10px', borderRadius: '8px', marginBottom: '15px' }}>
                        📍 <strong>Departamentos asignados:</strong> {allowedDepartments.join(', ')}
                    </div>
                ) : (
                    <div className="access-warning" style={{ background: 'var(--warning-bg)', padding: '15px', borderRadius: '8px', marginBottom: '15px', color: 'var(--warning-text)' }}>
                        ⚠️ <strong>Sin departamentos asignados.</strong> Contacte al administrador para obtener acceso a departamentos.
                    </div>
                )}

                {/* Formulario para agregar vehículo */}
                <div className="add-vehicle-section">
                    <h3>Agregar Nuevo Vehículo</h3>
                    <form onSubmit={handleAddVehicle} className="vehicle-form">
                        {message.text && (
                            <div className={`message ${message.type}`}>
                                {message.text}
                            </div>
                        )}
                        <div className="form-row">
                            <div className="form-group">
                                <label>Nombre del Vehículo</label>
                                <input
                                    name="name"
                                    value={vehicleForm.name}
                                    onChange={handleVehicleChange}
                                    placeholder="Ej: Camión 1"
                                />
                            </div>
                            <div className="form-group">
                                <label>Departamento</label>
                                <select value={selectedDepartment} onChange={handleDepartmentChange}>
                                    <option value="">-- Seleccionar Departamento --</option>
                                    {allowedDepartments.length > 0
                                        ? allowedDepartments.map(dept => (
                                            <option key={dept} value={dept}>{dept}</option>
                                        ))
                                        : DEPARTMENT_NAMES.map(dept => (
                                            <option key={dept} value={dept}>{dept}</option>
                                        ))
                                    }
                                </select>
                            </div>
                            <div className="form-group">
                                <label>Municipio</label>
                                <select name="city" value={vehicleForm.city} onChange={handleMunicipioSelect} disabled={!selectedDepartment}>
                                    <option value="">-- Seleccionar Municipio --</option>
                                    {selectedDepartment && getMunicipios(selectedDepartment).map(mun => (
                                        <option key={mun.name} value={mun.name}>{mun.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="form-group">
                                <label>Latitud</label>
                                <input
                                    name="lat"
                                    value={vehicleForm.lat}
                                    onChange={handleVehicleChange}
                                    placeholder="Clic en el mapa"
                                />
                            </div>
                            <div className="form-group">
                                <label>Longitud</label>
                                <input
                                    name="lng"
                                    value={vehicleForm.lng}
                                    onChange={handleVehicleChange}
                                    placeholder="Clic en el mapa"
                                />
                            </div>
                        </div>
                        <div className="form-actions">
                            <button type="button" onClick={handleGetCurrentLocation} className="location-btn">
                                📍 Usar Mi Ubicación
                            </button>
                            <button type="submit" className="submit-btn">
                                ➕ Agregar Vehículo
                            </button>
                        </div>
                    </form>
                </div>

                {/* Tabla de vehículos */}
                <div className="vehicles-section">
                    <h3>Vehículos Registrados ({vehicles.length})</h3>
                    <table className="vehicles-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th>Departamento</th>
                                <th>Municipio</th>
                                <th>Última Actualización</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {vehicles.map((vehicle) => (
                                <tr key={vehicle.id} className={editingVehicle?.id === vehicle.id ? 'editing' : ''}>
                                    {editingVehicle?.id === vehicle.id ? (
                                        <>
                                            <td>
                                                <input
                                                    type="text"
                                                    name="name"
                                                    value={editingVehicle.name}
                                                    onChange={handleEditVehicleChange}
                                                    className="edit-input"
                                                />
                                            </td>
                                            <td>
                                                {vehicle.lat && vehicle.lng
                                                    ? `${vehicle.lat.toFixed(4)}, ${vehicle.lng.toFixed(4)}`
                                                    : 'Sin ubicación'}
                                            </td>
                                            <td>
                                                <select
                                                    name="department"
                                                    value={editingVehicle.department || ''}
                                                    onChange={handleEditVehicleChange}
                                                    className="edit-input"
                                                >
                                                    <option value="">-- Depto --</option>
                                                    {allowedDepartments.length > 0
                                                        ? allowedDepartments.map(dept => (
                                                            <option key={dept} value={dept}>{dept}</option>
                                                        ))
                                                        : DEPARTMENT_NAMES.map(dept => (
                                                            <option key={dept} value={dept}>{dept}</option>
                                                        ))
                                                    }
                                                </select>
                                            </td>
                                            <td>
                                                <select
                                                    name="city"
                                                    value={editingVehicle.city || ''}
                                                    onChange={handleEditVehicleChange}
                                                    className="edit-input"
                                                    disabled={!editingVehicle.department}
                                                >
                                                    <option value="">-- Municipio --</option>
                                                    {editingVehicle.department && getMunicipios(editingVehicle.department).map(mun => (
                                                        <option key={mun.name} value={mun.name}>{mun.name}</option>
                                                    ))}
                                                </select>
                                            </td>
                                            <td>
                                                {vehicle.lastUpdate
                                                    ? new Date(vehicle.lastUpdate).toLocaleString()
                                                    : 'N/A'}
                                            </td>
                                            <td>
                                                <div className="action-buttons">
                                                    <button onClick={() => handleUpdateVehicle(vehicle.id)} className="save-btn">
                                                        💾 Guardar
                                                    </button>
                                                    <button onClick={cancelEditVehicle} className="cancel-btn">
                                                        ✖ Cancelar
                                                    </button>
                                                </div>
                                            </td>
                                        </>
                                    ) : (
                                        <>
                                            <td>{vehicle.name}</td>
                                            <td>
                                                {vehicle.lat && vehicle.lng
                                                    ? `${vehicle.lat.toFixed(4)}, ${vehicle.lng.toFixed(4)}`
                                                    : 'Sin ubicación'}
                                            </td>
                                            <td>{vehicle.department || '-'}</td>
                                            <td>{vehicle.city || '-'}</td>
                                            <td>
                                                {vehicle.lastUpdate
                                                    ? new Date(vehicle.lastUpdate).toLocaleString()
                                                    : 'N/A'}
                                            </td>
                                            <td>
                                                <div className="action-buttons">
                                                    <button onClick={() => startEditVehicle(vehicle)} className="edit-btn">
                                                        ✏️ Editar
                                                    </button>
                                                    <button onClick={() => handleFollowVehicle(vehicle)} className="follow-btn">
                                                        👀 Seguir
                                                    </button>
                                                    <button onClick={() => handleDeleteVehicle(vehicle.id)} className="delete-btn">
                                                        🗑️ Eliminar
                                                    </button>
                                                </div>
                                            </td>
                                        </>
                                    )}
                                </tr>
                            ))}
                            {vehicles.length === 0 && (
                                <tr>
                                    <td colSpan="4" className="no-data">
                                        No hay vehículos registrados
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Mapa integrado */}
                <div className="map-section">
                    <h3>🗺️ Seguimiento en Tiempo Real</h3>
                    <div className="map-container">
                        <MapComponent
                            socket={socket}
                            focusedMarker={focusedVehicle}
                            allowedCities={user?.assignedCities}
                            onMapClick={handleMapClick}
                            draftMarker={vehicleForm.lat && vehicleForm.lng ? { lat: parseFloat(vehicleForm.lat), lng: parseFloat(vehicleForm.lng) } : null}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SupervisorPanel;
