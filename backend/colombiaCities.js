// Database of major Colombian cities with their coordinate boundaries
// This allows for accurate city detection based on latitude/longitude

const colombiaCities = [
    {
        name: 'Bogotá',
        department: 'Cundinamarca',
        bounds: {
            latMin: 4.47,
            latMax: 4.84,
            lngMin: -74.22,
            lngMax: -73.99
        }
    },
    {
        name: 'Medellín',
        department: 'Antioquia',
        bounds: {
            latMin: 6.13,
            latMax: 6.36,
            lngMin: -75.64,
            lngMax: -75.49
        }
    },
    {
        name: 'Cali',
        department: 'Valle del Cauca',
        bounds: {
            latMin: 3.32,
            latMax: 3.54,
            lngMin: -76.59,
            lngMax: -76.45
        }
    },
    {
        name: 'Barranquilla',
        department: 'Atlántico',
        bounds: {
            latMin: 10.89,
            latMax: 11.08,
            lngMin: -74.88,
            lngMax: -74.73
        }
    },
    {
        name: 'Cartagena',
        department: 'Bolívar',
        bounds: {
            latMin: 10.32,
            latMax: 10.48,
            lngMin: -75.58,
            lngMax: -75.46
        }
    },
    {
        name: 'Cúcuta',
        department: 'Norte de Santander',
        bounds: {
            latMin: 7.85,
            latMax: 7.95,
            lngMin: -72.54,
            lngMax: -72.44
        }
    },
    {
        name: 'Bucaramanga',
        department: 'Santander',
        bounds: {
            latMin: 7.06,
            latMax: 7.16,
            lngMin: -73.15,
            lngMax: -73.08
        }
    },
    {
        name: 'Pereira',
        department: 'Risaralda',
        bounds: {
            latMin: 4.77,
            latMax: 4.85,
            lngMin: -75.73,
            lngMax: -75.64
        }
    },
    {
        name: 'Santa Marta',
        department: 'Magdalena',
        bounds: {
            latMin: 11.20,
            latMax: 11.27,
            lngMin: -74.24,
            lngMax: -74.17
        }
    },
    {
        name: 'Ibagué',
        department: 'Tolima',
        bounds: {
            latMin: 4.40,
            latMax: 4.48,
            lngMin: -75.25,
            lngMax: -75.16
        }
    },
    {
        name: 'Manizales',
        department: 'Caldas',
        bounds: {
            latMin: 5.03,
            latMax: 5.10,
            lngMin: -75.54,
            lngMax: -75.46
        }
    },
    {
        name: 'Villavicencio',
        department: 'Meta',
        bounds: {
            latMin: 4.10,
            latMax: 4.18,
            lngMin: -73.66,
            lngMax: -73.58
        }
    },
    {
        name: 'Pasto',
        department: 'Nariño',
        bounds: {
            latMin: 1.19,
            latMax: 1.24,
            lngMin: -77.31,
            lngMax: -77.25
        }
    },
    {
        name: 'Neiva',
        department: 'Huila',
        bounds: {
            latMin: 2.90,
            latMax: 2.97,
            lngMin: -75.32,
            lngMax: -75.26
        }
    },
    {
        name: 'Armenia',
        department: 'Quindío',
        bounds: {
            latMin: 4.51,
            latMax: 4.58,
            lngMin: -75.71,
            lngMax: -75.64
        }
    },
    {
        name: 'Popayán',
        department: 'Cauca',
        bounds: {
            latMin: 2.42,
            latMax: 2.48,
            lngMin: -76.63,
            lngMax: -76.56
        }
    },
    {
        name: 'Montería',
        department: 'Córdoba',
        bounds: {
            latMin: 8.73,
            latMax: 8.80,
            lngMin: -75.91,
            lngMax: -75.84
        }
    },
    {
        name: 'Valledupar',
        department: 'Cesar',
        bounds: {
            latMin: 10.44,
            latMax: 10.51,
            lngMin: -73.28,
            lngMax: -73.21
        }
    },
    {
        name: 'Sincelejo',
        department: 'Sucre',
        bounds: {
            latMin: 9.28,
            latMax: 9.33,
            lngMin: -75.41,
            lngMax: -75.36
        }
    },
    {
        name: 'Tunja',
        department: 'Boyacá',
        bounds: {
            latMin: 5.51,
            latMax: 5.56,
            lngMin: -73.38,
            lngMax: -73.33
        }
    },
    {
        name: 'Leticia',
        department: 'Amazonas',
        bounds: {
            latMin: -4.25,
            latMax: -4.18,
            lngMin: -69.98,
            lngMax: -69.90
        }
    }
];

// Colombia's approximate boundaries (expanded to include all territory)
const COLOMBIA_BOUNDS = {
    latMin: -4.3,
    latMax: 13.4,
    lngMin: -79.0,
    lngMax: -66.8
};

/**
 * Validates if coordinates are within Colombia's boundaries
 * @param {number} lat - Latitude
 * @param {number} lng - Longitude
 * @returns {boolean} - True if coordinates are in Colombia
 */
function isInColombia(lat, lng) {
    return lat >= COLOMBIA_BOUNDS.latMin &&
        lat <= COLOMBIA_BOUNDS.latMax &&
        lng >= COLOMBIA_BOUNDS.lngMin &&
        lng <= COLOMBIA_BOUNDS.lngMax;
}

/**
 * Detects the city based on coordinates
 * @param {number} lat - Latitude
 * @param {number} lng - Longitude
 * @returns {object|null} - City object with name and department, or null if not found
 */
function detectCity(lat, lng) {
    for (const city of colombiaCities) {
        if (lat >= city.bounds.latMin &&
            lat <= city.bounds.latMax &&
            lng >= city.bounds.lngMin &&
            lng <= city.bounds.lngMax) {
            return {
                name: city.name,
                department: city.department
            };
        }
    }

    // If no specific city found, return a generic "Colombia" location
    return {
        name: 'Colombia',
        department: 'Ubicación no identificada'
    };
}

module.exports = {
    colombiaCities,
    COLOMBIA_BOUNDS,
    isInColombia,
    detectCity
};
