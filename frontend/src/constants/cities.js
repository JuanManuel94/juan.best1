// Datos completos de Departamentos y Municipios de Colombia
// Organizados jerárquicamente para selección en cascada

import { COLOMBIA_DEPARTMENTS as PART1 } from './cities_part1';
import { COLOMBIA_DEPARTMENTS_PART2 as PART2 } from './cities_part2';
import { COLOMBIA_DEPARTMENTS_PART3 as PART3 } from './cities_part3';
import { COLOMBIA_DEPARTMENTS_PART4 as PART4 } from './cities_part4';

// Combinar todos los departamentos en un solo objeto
export const COLOMBIA_DEPARTMENTS = {
    ...PART1,
    ...PART2,
    ...PART3,
    ...PART4
};

// Lista ordenada de nombres de departamentos
export const DEPARTMENT_NAMES = Object.keys(COLOMBIA_DEPARTMENTS).sort();

// Función helper para obtener municipios de un departamento
export const getMunicipios = (departmentName) => {
    const dept = COLOMBIA_DEPARTMENTS[departmentName];
    return dept ? dept.municipios : [];
};

// Función helper para obtener datos de un municipio específico
export const getMunicipioData = (departmentName, municipioName) => {
    const municipios = getMunicipios(departmentName);
    return municipios.find(m => m.name === municipioName);
};

// Mantener exportación plana COLOMBIA_CITIES para compatibilidad con código existente
export const COLOMBIA_CITIES = Object.entries(COLOMBIA_DEPARTMENTS).flatMap(
    ([deptName, deptData]) =>
        deptData.municipios.map(m => ({
            name: m.name,
            department: deptName,
            lat: m.lat,
            lng: m.lng,
            bounds: {
                latMin: m.lat - 0.03,
                latMax: m.lat + 0.03,
                lngMin: m.lng - 0.03,
                lngMax: m.lng + 0.03
            }
        }))
);
