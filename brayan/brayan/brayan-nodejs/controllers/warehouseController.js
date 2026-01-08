const WarehouseModel = require('../models/WarehouseModel');
const BusinessModel = require('../models/BusinessModel');

const getViewData = (req, pageName, pageTitle) => ({
    page_name: pageName,
    page_title: pageTitle,
    userData: req.session.userData
});

// ==================== PRODUCTOS ====================

exports.products = async (req, res) => {
    try {
        const businessModel = new BusinessModel();
        const business = await businessModel.showBusiness();
        const categories = await WarehouseModel.getAllCategories();
        const suppliers = await WarehouseModel.getAllSuppliers();

        res.render('warehouse/products', {
            ...getViewData(req, 'Productos y Servicios', 'Productos'),
            business: business || {},
            categories,
            suppliers,
            page_functions_js: 'products.js'
        });
    } catch (error) {
        console.error('Warehouse page error:', error);
        res.status(500).render('errors/500');
    }
};

exports.listProducts = async (req, res) => {
    try {
        const products = await WarehouseModel.getAllProducts();
        res.json({ data: products });
    } catch (error) {
        console.error('List products error:', error);
        res.json({ data: [] });
    }
};

// ==================== CATEGORÍAS ====================

exports.categories = async (req, res) => {
    try {
        res.render('warehouse/categories', {
            ...getViewData(req, 'Tipos de Productos', 'Categorías de Productos'),
            page_functions_js: 'warehouse/categories.js'
        });
    } catch (error) {
        console.error('Categories page error:', error);
        res.status(500).render('errors/500');
    }
};

exports.listCategories = async (req, res) => {
    try {
        const categories = await WarehouseModel.getAllCategories();
        res.json({ data: categories });
    } catch (error) {
        console.error('List categories error:', error);
        res.json({ data: [] });
    }
};

exports.selectCategory = async (req, res) => {
    try {
        const category = await WarehouseModel.getCategoryById(req.params.id);
        res.json({ success: true, category });
    } catch (error) {
        res.status(500).json({ success: false, message: 'Error al obtener categoría' });
    }
};

exports.createCategory = async (req, res) => {
    try {
        await WarehouseModel.createCategory(req.body);
        res.json({ success: true, message: 'Categoría creada correctamente' });
    } catch (error) {
        console.error('Create category error:', error);
        res.status(500).json({ success: false, message: 'Error al crear categoría' });
    }
};

exports.updateCategory = async (req, res) => {
    try {
        await WarehouseModel.updateCategory(req.body.id, req.body);
        res.json({ success: true, message: 'Categoría actualizada' });
    } catch (error) {
        res.status(500).json({ success: false, message: 'Error al actualizar categoría' });
    }
};

exports.deleteCategory = async (req, res) => {
    try {
        await WarehouseModel.deleteCategory(req.body.id);
        res.json({ success: true, message: 'Categoría eliminada' });
    } catch (error) {
        res.status(500).json({ success: false, message: 'Error al eliminar categoría' });
    }
};

// ==================== PROVEEDORES ====================

exports.suppliers = async (req, res) => {
    try {
        res.render('warehouse/suppliers', {
            ...getViewData(req, 'Proveedores', 'Proveedores'),
            page_functions_js: 'warehouse/suppliers.js'
        });
    } catch (error) {
        console.error('Suppliers page error:', error);
        res.status(500).render('errors/500');
    }
};

exports.listSuppliers = async (req, res) => {
    try {
        const suppliers = await WarehouseModel.getAllSuppliers();
        res.json({ data: suppliers });
    } catch (error) {
        console.error('List suppliers error:', error);
        res.json({ data: [] });
    }
};

exports.selectSupplier = async (req, res) => {
    try {
        const supplier = await WarehouseModel.getSupplierById(req.params.id);
        res.json({ success: true, supplier });
    } catch (error) {
        res.status(500).json({ success: false, message: 'Error al obtener proveedor' });
    }
};

exports.createSupplier = async (req, res) => {
    try {
        await WarehouseModel.createSupplier(req.body);
        res.json({ success: true, message: 'Proveedor creado correctamente' });
    } catch (error) {
        console.error('Create supplier error:', error);
        res.status(500).json({ success: false, message: 'Error al crear proveedor' });
    }
};

exports.updateSupplier = async (req, res) => {
    try {
        await WarehouseModel.updateSupplier(req.body.id, req.body);
        res.json({ success: true, message: 'Proveedor actualizado' });
    } catch (error) {
        res.status(500).json({ success: false, message: 'Error al actualizar proveedor' });
    }
};

exports.deleteSupplier = async (req, res) => {
    try {
        await WarehouseModel.deleteSupplier(req.body.id);
        res.json({ success: true, message: 'Proveedor eliminado' });
    } catch (error) {
        res.status(500).json({ success: false, message: 'Error al eliminar proveedor' });
    }
};
