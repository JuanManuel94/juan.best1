import React, { useState, useEffect } from 'react';
import './TrackerComponent.css';

const TrackerComponent = ({ socket }) => {
    const [id, setId] = useState('');
    const [type, setType] = useState('vehicle');
    const [joined, setJoined] = useState(false);
    const [status, setStatus] = useState('Idle');
    const [options, setOptions] = useState([]);
    const [currentLocation, setCurrentLocation] = useState(null);
    const [updateCount, setUpdateCount] = useState(0);
    const [watchId, setWatchId] = useState(null);

    useEffect(() => {
        // Fetch options based on type
        const fetchOptions = async () => {
            try {
                const endpoint = type === 'vehicle'
                    ? 'http://localhost:3000/api/vehicles'
                    : 'http://localhost:3000/api/supervisors';
                const res = await fetch(endpoint);
                const data = await res.json();
                setOptions(data);
                setId('');
            } catch (error) {
                console.error('Error fetching options:', error);
            }
        };

        fetchOptions();
    }, [type]);

    const handleJoin = () => {
        if (id) {
            const selectedItem = options.find(item => item.id === id);
            if (selectedItem) {
                socket.emit('join', { id: selectedItem.name, type });
                setJoined(true);
                startTracking();
            }
        }
    };

    const startTracking = () => {
        setStatus('Iniciando GPS...');
        if (navigator.geolocation) {
            const wId = navigator.geolocation.watchPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    socket.emit('updateLocation', { lat: latitude, lng: longitude });
                    setCurrentLocation({ lat: latitude, lng: longitude });
                    setUpdateCount(prev => prev + 1);
                    setStatus('Rastreando activamente');
                },
                (error) => {
                    console.error(error);
                    setStatus('Error obteniendo ubicación');
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 5000
                }
            );
            setWatchId(wId);
        } else {
            setStatus('Geolocalización no soportada');
        }
    };

    const handleStop = () => {
        if (watchId) {
            navigator.geolocation.clearWatch(watchId);
        }
        setJoined(false);
        setStatus('Detenido');
        setCurrentLocation(null);
        setUpdateCount(0);
        setWatchId(null);
    };

    if (!joined) {
        return (
            <div className="tracker-container">
                <div className="tracker-card">
                    <div className="card-header">
                        <h2 className="card-title">🚀 Driver App</h2>
                        <p className="card-subtitle">Inicia el rastreo en tiempo real</p>
                    </div>

                    <div className="form-section">
                        <div className="form-group">
                            <label className="form-label">
                                <span className="label-icon">📋</span>
                                Selecciona el tipo
                            </label>
                            <select
                                className="input-field"
                                value={type}
                                onChange={(e) => setType(e.target.value)}
                            >
                                <option value="vehicle">🚗 Vehículo</option>
                                <option value="supervisor">👤 Supervisor</option>
                            </select>
                        </div>

                        <div className="form-group">
                            <label className="form-label">
                                <span className="label-icon">{type === 'vehicle' ? '🚗' : '👤'}</span>
                                Selecciona {type === 'vehicle' ? 'vehículo' : 'supervisor'}
                            </label>
                            <select
                                className="input-field"
                                value={id}
                                onChange={(e) => setId(e.target.value)}
                                disabled={options.length === 0}
                            >
                                <option value="">-- Seleccionar --</option>
                                {options.map(item => (
                                    <option key={item.id} value={item.id}>
                                        {item.name}
                                    </option>
                                ))}
                            </select>
                            {options.length === 0 && (
                                <p className="helper-text">
                                    ⚠️ No hay {type === 'vehicle' ? 'vehículos' : 'supervisores'} disponibles
                                </p>
                            )}
                        </div>

                        <button
                            onClick={handleJoin}
                            className="action-btn primary-btn"
                            disabled={!id}
                        >
                            <span className="btn-icon">📍</span>
                            Iniciar Rastreo
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="tracker-container">
            <div className="tracker-card active">
                <div className="card-header">
                    <h2 className="card-title">✅ Rastreo Activo</h2>
                    <div className="status-badge">
                        <div className="status-indicator pulse"></div>
                        <span>En línea</span>
                    </div>
                </div>

                <div className="tracking-info">
                    <div className="info-card">
                        <div className="info-icon">{type === 'vehicle' ? '🚗' : '👤'}</div>
                        <div className="info-content">
                            <div className="info-label">Identificación</div>
                            <div className="info-value">{options.find(o => o.id === id)?.name || id}</div>
                        </div>
                    </div>

                    <div className="info-card">
                        <div className="info-icon">📊</div>
                        <div className="info-content">
                            <div className="info-label">Estado</div>
                            <div className="info-value">{status}</div>
                        </div>
                    </div>

                    <div className="info-card">
                        <div className="info-icon">🔄</div>
                        <div className="info-content">
                            <div className="info-label">Actualizaciones enviadas</div>
                            <div className="info-value">{updateCount}</div>
                        </div>
                    </div>

                    {currentLocation && (
                        <div className="location-card">
                            <div className="location-header">
                                <span className="location-icon">📍</span>
                                <span className="location-title">Ubicación Actual</span>
                            </div>
                            <div className="coordinates">
                                <div className="coordinate-row">
                                    <span className="coord-label">Latitud:</span>
                                    <span className="coord-value">{currentLocation.lat.toFixed(6)}</span>
                                </div>
                                <div className="coordinate-row">
                                    <span className="coord-label">Longitud:</span>
                                    <span className="coord-value">{currentLocation.lng.toFixed(6)}</span>
                                </div>
                            </div>
                        </div>
                    )}
                </div>

                <button
                    onClick={handleStop}
                    className="action-btn danger-btn"
                >
                    <span className="btn-icon">⏹️</span>
                    Detener Rastreo
                </button>
            </div>
        </div>
    );
};

export default TrackerComponent;
