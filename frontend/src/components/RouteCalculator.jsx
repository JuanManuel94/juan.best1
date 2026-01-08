import React, { useState } from 'react';
import './RouteCalculator.css';

const RouteCalculator = ({ vehicles, onRouteCalculated, onClearRoute }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [originId, setOriginId] = useState('');
    const [destinationId, setDestinationId] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [routeInfo, setRouteInfo] = useState(null);

    const calculateRoute = async () => {
        if (!originId || !destinationId) {
            setError('Selecciona origen y destino');
            return;
        }

        if (originId === destinationId) {
            setError('Origen y destino deben ser diferentes');
            return;
        }

        const origin = vehicles.find(v => v.id === originId);
        const destination = vehicles.find(v => v.id === destinationId);

        if (!origin || !destination) {
            setError('Vehículos no encontrados');
            return;
        }

        setLoading(true);
        setError('');
        setRouteInfo(null);

        try {
            // Use OSRM public API for route calculation
            const url = `https://router.project-osrm.org/route/v1/driving/${origin.lng},${origin.lat};${destination.lng},${destination.lat}?overview=full&geometries=geojson`;

            const response = await fetch(url);
            const data = await response.json();

            if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                throw new Error('No se pudo calcular la ruta');
            }

            const route = data.routes[0];
            const distanceKm = (route.distance / 1000).toFixed(2);
            const durationMin = Math.round(route.duration / 60);
            const hours = Math.floor(durationMin / 60);
            const mins = durationMin % 60;

            // Convert GeoJSON coordinates to Leaflet format [lat, lng]
            const coordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);

            setRouteInfo({
                distance: `${distanceKm} km`,
                duration: hours > 0 ? `${hours}h ${mins}min` : `${mins} min`,
                originName: origin.name,
                destinationName: destination.name
            });

            // Pass the route coordinates to parent
            if (onRouteCalculated) {
                onRouteCalculated(coordinates, {
                    origin,
                    destination,
                    distance: distanceKm,
                    duration: durationMin
                });
            }
        } catch (err) {
            console.error('Route calculation error:', err);
            setError('Error al calcular la ruta. Intenta de nuevo.');
        } finally {
            setLoading(false);
        }
    };

    const handleClear = () => {
        setOriginId('');
        setDestinationId('');
        setRouteInfo(null);
        setError('');
        if (onClearRoute) {
            onClearRoute();
        }
    };

    if (!isOpen) {
        return (
            <button
                className="route-toggle-btn minimized"
                onClick={() => setIsOpen(true)}
            >
                🛤️ Calcular Ruta
            </button>
        );
    }

    return (
        <div className="route-calculator">
            <div className="route-calculator-header">
                <h3>🛤️ Calcular Ruta</h3>
                <button
                    className="route-close-btn"
                    onClick={() => setIsOpen(false)}
                >
                    ✕
                </button>
            </div>

            <div className="route-form">
                <div className="route-field">
                    <label>📍 Origen</label>
                    <select
                        value={originId}
                        onChange={(e) => setOriginId(e.target.value)}
                    >
                        <option value="">Seleccionar vehículo...</option>
                        {vehicles.filter(v => v.lat && v.lng).map(vehicle => (
                            <option key={vehicle.id} value={vehicle.id}>
                                {vehicle.name} - {vehicle.city || 'Sin ciudad'}
                            </option>
                        ))}
                    </select>
                </div>

                <div className="route-field">
                    <label>🏁 Destino</label>
                    <select
                        value={destinationId}
                        onChange={(e) => setDestinationId(e.target.value)}
                    >
                        <option value="">Seleccionar vehículo...</option>
                        {vehicles.filter(v => v.lat && v.lng).map(vehicle => (
                            <option key={vehicle.id} value={vehicle.id}>
                                {vehicle.name} - {vehicle.city || 'Sin ciudad'}
                            </option>
                        ))}
                    </select>
                </div>

                <div className="route-actions">
                    <button
                        className="route-calculate-btn"
                        onClick={calculateRoute}
                        disabled={loading || !originId || !destinationId}
                    >
                        {loading ? (
                            <>
                                <span className="spinner"></span>
                                Calculando...
                            </>
                        ) : (
                            <>🚗 Calcular</>
                        )}
                    </button>
                    <button
                        className="route-clear-btn"
                        onClick={handleClear}
                    >
                        🗑️
                    </button>
                </div>

                {error && (
                    <div className="route-error">
                        ⚠️ {error}
                    </div>
                )}

                {routeInfo && (
                    <div className="route-result">
                        <div className="route-result-row">
                            <span className="route-result-label">Ruta:</span>
                            <span className="route-result-value">{routeInfo.originName} → {routeInfo.destinationName}</span>
                        </div>
                        <div className="route-result-row">
                            <span className="route-result-label">📏 Distancia:</span>
                            <span className="route-result-value">{routeInfo.distance}</span>
                        </div>
                        <div className="route-result-row">
                            <span className="route-result-label">⏱️ Tiempo:</span>
                            <span className="route-result-value">{routeInfo.duration}</span>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default RouteCalculator;
