import React, { useEffect, useState } from 'react';
import { MapContainer, TileLayer, Marker, Popup, useMap, useMapEvents, Polyline, Circle, LayersControl } from 'react-leaflet';
import MarkerClusterGroup from 'react-leaflet-cluster';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Custom icons for vehicles and supervisors
const vehicleIcon = L.divIcon({
    className: 'custom-marker',
    html: `<div class="marker-pin vehicle-marker"><span class="marker-icon">🚗</span></div>`,
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40]
});

const supervisorIcon = L.divIcon({
    className: 'custom-marker',
    html: `<div class="marker-pin supervisor-marker"><span class="marker-icon">👤</span></div>`,
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40]
});

// Component that centers and zooms the map on a selected marker
const MapController = ({ selectedMarker }) => {
    const map = useMap();
    useEffect(() => {
        if (selectedMarker && selectedMarker.lat && selectedMarker.lng) {
            map.flyTo([selectedMarker.lat, selectedMarker.lng], 18, {
                duration: 1.5,
                easeLinearity: 0.25,
            });
        }
    }, [selectedMarker, map]);
    return null;
};

// Component to handle map clicks
const MapClickHandler = ({ onMapClick }) => {
    useMapEvents({
        click: (e) => {
            if (onMapClick) {
                onMapClick(e.latlng);
            }
        },
    });
    return null;
};

const playbackIcon = L.divIcon({
    className: 'custom-marker',
    html: `<div class="marker-pin playback-marker"><span class="marker-icon">🚕</span></div>`,
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40]
});

const draftIcon = L.divIcon({
    className: 'custom-marker',
    html: `<div class="marker-pin draft-marker"><span class="marker-icon">📍</span></div>`,
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40]
});

const MapComponent = ({ socket, focusedMarker, allowedCities, historyPath, geofences, onMapClick, playbackPosition, draftMarker, items, calculatedRoute }) => {
    const [markers, setMarkers] = useState([]);
    const [displayMarkers, setDisplayMarkers] = useState([]);
    const [selectedMarker, setSelectedMarker] = useState(null);

    // When the parent sends a focusedMarker (from "Seguir" button), select it
    useEffect(() => {
        if (focusedMarker) {
            setSelectedMarker(focusedMarker);
        }
    }, [focusedMarker]);

    // Listen for real‑time location updates from the backend
    useEffect(() => {
        socket.on('locationsUpdate', data => setMarkers(data));
        return () => socket.off('locationsUpdate');
    }, [socket]);

    // Merge socket markers with items prop
    useEffect(() => {
        if (items && items.length > 0) {
            // Use items directly if provided (for AdminPanel static display)
            setMarkers(items);
        }
    }, [items]);

    // Process markers to handle overlaps (Jitter/Displace)
    useEffect(() => {
        const processMarkers = () => {
            const processed = [];
            const groups = {};

            // Group markers by close proximity
            markers.forEach(marker => {
                if (!marker.lat || !marker.lng) return;

                // Filter by allowedCities if provided and not empty
                if (allowedCities && allowedCities.length > 0) {
                    if (marker.city && !allowedCities.includes(marker.city)) {
                        return;
                    }
                }

                const key = `${marker.lat.toFixed(4)},${marker.lng.toFixed(4)}`; // Group by ~11m proximity
                if (!groups[key]) groups[key] = [];
                groups[key].push(marker);
            });

            Object.values(groups).forEach(group => {
                if (group.length === 1) {
                    processed.push({ ...group[0], displayLat: group[0].lat, displayLng: group[0].lng });
                } else {
                    // Spiral/Circle layout for overlapping markers
                    const angleStep = (2 * Math.PI) / group.length;
                    const radius = 0.0003; // Approx 30m radius

                    group.forEach((marker, index) => {
                        const angle = index * angleStep;
                        processed.push({
                            ...marker,
                            displayLat: marker.lat + radius * Math.cos(angle),
                            displayLng: marker.lng + radius * Math.sin(angle)
                        });
                    });
                }
            });
            setDisplayMarkers(processed);
        };

        processMarkers();
    }, [markers, allowedCities]);

    const handleMarkerClick = marker => setSelectedMarker(marker);

    return (
        <>
            <style>{`
        .custom-marker { background: transparent; border: none; }
        .marker-pin { width: 40px; height: 40px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0,0,0,0.3); transition: all 0.3s ease; animation: bounce 2s infinite; }
        .marker-pin:hover { transform: rotate(-45deg) scale(1.1); box-shadow: 0 6px 12px rgba(0,0,0,0.4); }
        .vehicle-marker { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .supervisor-marker { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .playback-marker { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); z-index: 1000; }
        .draft-marker { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); opacity: 0.8; }
        .marker-icon { transform: rotate(45deg); font-size: 20px; }
        @keyframes bounce { 0%,100% { transform: rotate(-45deg) translateY(0); } 50% { transform: rotate(-45deg) translateY(-5px); } }
        .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); padding:0; overflow:hidden; }
        .leaflet-popup-content { margin:0; min-width:200px; }
        .popup-header { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color:white; padding:12px 16px; font-weight:700; font-size:16px; border-bottom:2px solid #1e40af; }
        .popup-header.supervisor { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-bottom:2px solid #047857; }
        .popup-body { padding:12px 16px; background:white; }
        .popup-row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #e2e8f0; }
        .popup-row:last-child { border-bottom:none; }
        .popup-label { font-weight:600; color:#64748b; font-size:13px; }
        .popup-value { color:#1e293b; font-size:13px; }
        .leaflet-control-zoom { border:none !important; box-shadow:0 4px 12px rgba(0,0,0,0.15) !important; }
        .leaflet-control-zoom a { background:white !important; color:#3b82f6 !important; border:none !important; font-size:20px !important; font-weight:bold !important; transition:all 0.3s ease !important; }
        .leaflet-control-zoom a:hover { background:#3b82f6 !important; color:white !important; transform:scale(1.1); }
        /* Cluster styles */
        .marker-cluster-small { background-color: rgba(59, 130, 246, 0.6); }
        .marker-cluster-small div { background-color: rgba(59, 130, 246, 0.9); }
        .marker-cluster-medium { background-color: rgba(245, 158, 11, 0.6); }
        .marker-cluster-medium div { background-color: rgba(245, 158, 11, 0.9); }
        .marker-cluster-large { background-color: rgba(239, 68, 68, 0.6); }
        .marker-cluster-large div { background-color: rgba(239, 68, 68, 0.9); }
        .marker-cluster { border-radius: 50%; font-weight: bold; font-size: 14px; color: white; display: flex; align-items: center; justify-content: center; }
        .marker-cluster div { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .leaflet-control-layers { border-radius: 8px !important; box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important; }
        .leaflet-control-layers-toggle { width: 36px !important; height: 36px !important; }
      `}</style>
            <MapContainer center={[4.6097, -74.0817]} zoom={6} style={{ height: '100%', width: '100%' }} zoomControl={true}>
                <LayersControl position="topright">
                    <LayersControl.BaseLayer name="🗺️ Mapa Normal">
                        <TileLayer
                            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            maxZoom={19}
                        />
                    </LayersControl.BaseLayer>
                    <LayersControl.BaseLayer name="🛰️ Satélite">
                        <TileLayer
                            url="https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}"
                            attribution='&copy; Esri'
                            maxZoom={19}
                        />
                    </LayersControl.BaseLayer>
                    <LayersControl.BaseLayer name="⛰️ Terreno">
                        <TileLayer
                            url="https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png"
                            attribution='&copy; OpenTopoMap'
                            maxZoom={17}
                        />
                    </LayersControl.BaseLayer>
                    <LayersControl.BaseLayer checked name="🌍 Híbrido">
                        <TileLayer
                            url="http://mt{s}.google.com/vt/lyrs=y&hl=es&x={x}&y={y}&z={z}"
                            subdomains={['0', '1', '2', '3']}
                            attribution="&copy; Google Maps"
                            maxZoom={20}
                        />
                    </LayersControl.BaseLayer>
                </LayersControl>
                <MapController selectedMarker={selectedMarker} />
                <MapClickHandler onMapClick={onMapClick} />

                {/* Render History Path */}
                {historyPath && historyPath.length > 1 && (
                    <Polyline
                        positions={historyPath}
                        color="blue"
                        weight={5}
                        opacity={0.7}
                        dashArray="10, 10"
                    />
                )}

                {/* Render Calculated Route */}
                {calculatedRoute && calculatedRoute.length > 1 && (
                    <Polyline
                        positions={calculatedRoute}
                        color="#10b981"
                        weight={6}
                        opacity={0.9}
                    />
                )}

                {/* Render Playback Marker */}
                {playbackPosition && (
                    <Marker
                        position={[playbackPosition.lat, playbackPosition.lng]}
                        icon={playbackIcon}
                        zIndexOffset={1000}
                    >
                        <Popup>
                            <div className="popup-header" style={{ background: '#f59e0b' }}>
                                🚕 Reproducción
                            </div>
                            <div className="popup-body">
                                <div className="popup-row"><span className="popup-label">Tiempo:</span><span className="popup-value">{playbackPosition.timestamp ? new Date(playbackPosition.timestamp).toLocaleString() : 'N/A'}</span></div>
                            </div>
                        </Popup>
                    </Marker>
                )}

                {/* Render Draft Marker (New Vehicle Location) */}
                {draftMarker && draftMarker.lat && draftMarker.lng && (
                    <Marker
                        position={[draftMarker.lat, draftMarker.lng]}
                        icon={draftIcon}
                        zIndexOffset={900}
                    >
                        <Popup>
                            <div className="popup-header" style={{ background: '#6366f1' }}>
                                📍 Nueva Ubicación
                            </div>
                            <div className="popup-body">
                                <div className="popup-row"><span className="popup-label">Latitud:</span><span className="popup-value">{parseFloat(draftMarker.lat).toFixed(6)}</span></div>
                                <div className="popup-row"><span className="popup-label">Longitud:</span><span className="popup-value">{parseFloat(draftMarker.lng).toFixed(6)}</span></div>
                            </div>
                        </Popup>
                    </Marker>
                )}

                {/* Render Geofences */}
                {geofences && geofences.map(fence => (
                    <Circle
                        key={fence.id}
                        center={[fence.lat, fence.lng]}
                        radius={fence.radius}
                        pathOptions={{ color: 'red', fillColor: 'red', fillOpacity: 0.1 }}
                    >
                        <Popup>
                            <strong>{fence.name}</strong><br />
                            {fence.description}
                        </Popup>
                    </Circle>
                ))}

                {/* Render all processed markers with clustering */}
                <MarkerClusterGroup
                    chunkedLoading
                    spiderfyOnMaxZoom={true}
                    showCoverageOnHover={false}
                    zoomToBoundsOnClick={true}
                    maxClusterRadius={50}
                    iconCreateFunction={(cluster) => {
                        const count = cluster.getChildCount();
                        let size = 'small';
                        if (count >= 10) size = 'medium';
                        if (count >= 50) size = 'large';
                        return L.divIcon({
                            html: `<div><span>${count}</span></div>`,
                            className: `marker-cluster marker-cluster-${size}`,
                            iconSize: L.point(40, 40)
                        });
                    }}
                >
                    {displayMarkers.map(marker => (
                        <Marker
                            key={marker.id}
                            position={[marker.displayLat, marker.displayLng]}
                            icon={marker.type === 'vehicle' ? vehicleIcon : supervisorIcon}
                            eventHandlers={{ click: () => handleMarkerClick(marker) }}
                        >
                            <Popup>
                                <div className={`popup-header ${marker.type === 'supervisor' ? 'supervisor' : ''}`}>
                                    {marker.type === 'vehicle' ? '🚗' : '👤'} {marker.name || marker.id}
                                </div>
                                <div className="popup-body">
                                    <div className="popup-row"><span className="popup-label">Tipo:</span><span className="popup-value">{marker.type === 'vehicle' ? 'Vehículo' : 'Supervisor'}</span></div>
                                    {marker.city && (
                                        <div className="popup-row"><span className="popup-label">Ciudad:</span><span className="popup-value">{marker.city}, {marker.department}</span></div>
                                    )}
                                    <div className="popup-row"><span className="popup-label">Latitud:</span><span className="popup-value">{marker.lat.toFixed(6)}</span></div>
                                    <div className="popup-row"><span className="popup-label">Longitud:</span><span className="popup-value">{marker.lng.toFixed(6)}</span></div>
                                    <div className="popup-row"><span className="popup-label">Última actualización:</span><span className="popup-value">{new Date(marker.lastUpdate).toLocaleTimeString()}</span></div>
                                </div>
                            </Popup>
                        </Marker>
                    ))}
                </MarkerClusterGroup>
                {/* If a focused marker is not part of the live list, render it separately */}
                {selectedMarker && selectedMarker.lat && selectedMarker.lng && !displayMarkers.find(m => m.id === selectedMarker.id) && (
                    <Marker
                        key={`focused-${selectedMarker.id}`}
                        position={[selectedMarker.lat, selectedMarker.lng]}
                        icon={selectedMarker.type === 'vehicle' ? vehicleIcon : supervisorIcon}
                        eventHandlers={{ click: () => handleMarkerClick(selectedMarker) }}
                    >
                        <Popup>
                            <div className={`popup-header ${selectedMarker.type === 'supervisor' ? 'supervisor' : ''}`}>
                                {selectedMarker.type === 'vehicle' ? '🚗' : '👤'} {selectedMarker.name || selectedMarker.id}
                            </div>
                            <div className="popup-body">
                                <div className="popup-row"><span className="popup-label">Tipo:</span><span className="popup-value">{selectedMarker.type === 'vehicle' ? 'Vehículo' : 'Supervisor'}</span></div>
                                {selectedMarker.city && (
                                    <div className="popup-row"><span className="popup-label">Ciudad:</span><span className="popup-value">{selectedMarker.city}, {selectedMarker.department}</span></div>
                                )}
                                <div className="popup-row"><span className="popup-label">Latitud:</span><span className="popup-value">{selectedMarker.lat.toFixed(6)}</span></div>
                                <div className="popup-row"><span className="popup-label">Longitud:</span><span className="popup-value">{selectedMarker.lng.toFixed(6)}</span></div>
                                {selectedMarker.lastUpdate && (
                                    <div className="popup-row"><span className="popup-label">Última actualización:</span><span className="popup-value">{new Date(selectedMarker.lastUpdate).toLocaleTimeString()}</span></div>
                                )}
                            </div>
                        </Popup>
                    </Marker>
                )}
            </MapContainer>
        </>
    );
};

export default MapComponent;
