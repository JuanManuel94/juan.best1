const TasksModel = require('../models/TasksModel');
const UsersModel = require('../models/UsersModel');
const usersModel = new UsersModel();

const getViewData = (req, pageName, pageTitle) => ({
    page_name: pageName,
    page_title: pageTitle,
    userData: req.session.userData
});

// Página principal de tareas
exports.tasks = async (req, res) => {
    try {
        let stats = { total: 0, pending: 0, in_progress: 0, completed: 0, urgent: 0 };
        let users = [];

        try {
            stats = await TasksModel.getTaskStats() || stats;
        } catch (e) {
            console.error('Error getting task stats:', e.message);
        }

        try {
            users = await usersModel.listRecords(1) || [];
        } catch (e) {
            console.error('Error getting users:', e.message);
        }

        res.render('tasks/index', {
            ...getViewData(req, 'Tareas', 'Gestión de Tareas'),
            stats,
            users,
            page_functions_js: 'tasks.js'
        });
    } catch (error) {
        console.error('Error loading tasks:', error);
        res.status(500).render('errors/500');
    }
};

// Listar tareas (API)
exports.list = async (req, res) => {
    try {
        const filters = {
            status: req.query.status || null,
            priority: req.query.priority || null,
            assigned_to: req.query.assigned_to || null,
            task_type: req.query.task_type || null
        };
        const tasks = await TasksModel.getAllTasks(filters);
        res.json({ data: tasks });
    } catch (error) {
        console.error('Error listing tasks:', error);
        res.status(500).json({ error: 'Error al cargar tareas' });
    }
};

// Obtener tarea por ID
exports.selectRecord = async (req, res) => {
    try {
        const task = await TasksModel.getTaskById(req.params.id);
        res.json({ success: true, task });
    } catch (error) {
        console.error('Error getting task:', error);
        res.status(500).json({ success: false, message: 'Error al obtener tarea' });
    }
};

// Crear tarea
exports.create = async (req, res) => {
    try {
        const data = {
            ...req.body,
            created_by: req.session.userData.id
        };
        await TasksModel.createTask(data);
        res.json({ success: true, message: 'Tarea creada correctamente' });
    } catch (error) {
        console.error('Error creating task:', error);
        res.status(500).json({ success: false, message: 'Error al crear tarea' });
    }
};

// Actualizar tarea
exports.update = async (req, res) => {
    try {
        await TasksModel.updateTask(req.body.id, req.body);
        res.json({ success: true, message: 'Tarea actualizada correctamente' });
    } catch (error) {
        console.error('Error updating task:', error);
        res.status(500).json({ success: false, message: 'Error al actualizar tarea' });
    }
};

// Cambiar estado
exports.changeStatus = async (req, res) => {
    try {
        const { id, status } = req.body;
        await TasksModel.updateTask(id, { status });
        res.json({ success: true, message: 'Estado actualizado' });
    } catch (error) {
        console.error('Error changing status:', error);
        res.status(500).json({ success: false, message: 'Error al cambiar estado' });
    }
};

// Eliminar tarea
exports.remove = async (req, res) => {
    try {
        await TasksModel.deleteTask(req.body.id);
        res.json({ success: true, message: 'Tarea eliminada' });
    } catch (error) {
        console.error('Error deleting task:', error);
        res.status(500).json({ success: false, message: 'Error al eliminar tarea' });
    }
};
