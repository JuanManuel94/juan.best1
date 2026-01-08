
import React, { useState, useEffect } from 'react';
import io from 'socket.io-client';
import MapComponent from './components/MapComponent';
import TrackerComponent from './components/TrackerComponent';
import LoginComponent from './components/LoginComponent';
import AdminPanel from './components/AdminPanel';
import SupervisorPanel from './components/SupervisorPanel';
import './App.css';

// Connect to backend
const socket = io('http://localhost:3000');

function App() {
  const [mode, setMode] = useState('dashboard'); // 'dashboard', 'tracker', 'admin', 'supervisor'
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [user, setUser] = useState(null); // { name, cedula, role: 'admin' | 'supervisor' }

  const handleLogin = (userData) => {
    setUser(userData);
    setIsAuthenticated(true);
    // Redirect based on role
    if (userData.role === 'admin') {
      setMode('admin');
    } else if (userData.role === 'supervisor') {
      setMode('supervisor');
    }
  };

  const handleLogout = () => {
    setIsAuthenticated(false);
    setUser(null);
    setMode('dashboard');
  };

  const renderContent = () => {
    if (mode === 'tracker') {
      return (
        <div className="tracker-wrapper">
          <TrackerComponent socket={socket} />
        </div>
      );
    }

    if (mode === 'supervisor') {
      if (isAuthenticated && user?.role === 'supervisor') {
        return <SupervisorPanel socket={socket} user={user} onLogout={handleLogout} />;
      }
      return <LoginComponent onLogin={handleLogin} />;
    }

    if (mode === 'admin') {
      if (isAuthenticated && user?.role === 'admin') {
        return <AdminPanel socket={socket} />;
      }
      return <LoginComponent onLogin={handleLogin} />;
    }

    // Dashboard mode
    if (isAuthenticated) {
      return <MapComponent socket={socket} />;
    }
    return <LoginComponent onLogin={handleLogin} />;
  };

  return (
    <div className="app-container">
      <nav className="navbar">
        <h1 className="logo">DriveGo</h1>
        <div className="nav-buttons">
          <button
            onClick={() => setMode('dashboard')}
            className={`nav-btn ${mode === 'dashboard' ? 'active' : ''}`}
          >
            Dashboard
          </button>
          <button
            onClick={() => setMode('tracker')}
            className={`nav-btn ${mode === 'tracker' ? 'active' : ''}`}
          >
            Driver App
          </button>
          {isAuthenticated && user?.role === 'supervisor' && (
            <button
              onClick={() => setMode('supervisor')}
              className={`nav-btn ${mode === 'supervisor' ? 'active' : ''}`}
            >
              Panel Supervisor
            </button>
          )}
          {isAuthenticated && user?.role === 'admin' && (
            <>
              <button
                onClick={() => setMode('admin')}
                className={`nav-btn ${mode === 'admin' ? 'active' : ''}`}
              >
                Admin Panel
              </button>
              <button
                onClick={handleLogout}
                className="nav-btn logout-btn"
              >
                🚪 Cerrar Sesión
              </button>
            </>
          )}
        </div>
      </nav>

      <main className="main-content">
        {renderContent()}
      </main>
    </div>
  );
}

export default App;
