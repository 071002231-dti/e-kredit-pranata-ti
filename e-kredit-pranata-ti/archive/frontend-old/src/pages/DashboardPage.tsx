import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { dashboardService } from '../services/dashboardService';
import { DashboardStats, CategorySummary } from '../types';

const DashboardPage: React.FC = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [summary, setSummary] = useState<CategorySummary[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    loadDashboardData();
  }, []);

  const loadDashboardData = async () => {
    try {
      setLoading(true);
      const [statsData, summaryData] = await Promise.all([
        dashboardService.getStats(),
        dashboardService.getSummary(),
      ]);
      setStats(statsData);
      setSummary(summaryData);
    } catch (err: any) {
      setError('Failed to load dashboard data');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  if (loading) {
    return <div style={{ padding: '20px' }}>Loading...</div>;
  }

  return (
    <div style={{ padding: '20px', maxWidth: '1200px', margin: '0 auto' }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '30px' }}>
        <div>
          <h1>Dashboard - e-Kredit Pranata TI</h1>
          <p>Welcome, <strong>{user?.name}</strong> ({user?.role})</p>
          {user?.jenjang_jabatan && (
            <p style={{ margin: '5px 0', color: '#6c757d' }}>
              <strong>Jenjang:</strong> {user.jenjang_jabatan} | <strong>Golongan:</strong> {user.golongan}
            </p>
          )}
        </div>
        <button
          onClick={handleLogout}
          style={{
            padding: '8px 16px',
            backgroundColor: '#dc3545',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer'
          }}
        >
          Logout
        </button>
      </div>

      {error && <div style={{ color: 'red', marginBottom: '20px' }}>{error}</div>}

      {/* Progress Bar */}
      {stats && stats.target_angka_kredit && parseFloat(stats.target_angka_kredit) > 0 && (
        <div style={{ marginBottom: '30px', padding: '20px', backgroundColor: '#f8f9fa', borderRadius: '8px', border: '1px solid #dee2e6' }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '10px' }}>
            <h3 style={{ margin: 0 }}>Progress Target Angka Kredit</h3>
            <span style={{ fontWeight: 'bold', fontSize: '18px', color: stats.is_compliant ? '#28a745' : '#ffc107' }}>
              {stats.progress_percentage.toFixed(1)}%
            </span>
          </div>
          <div style={{ width: '100%', height: '30px', backgroundColor: '#e9ecef', borderRadius: '15px', overflow: 'hidden' }}>
            <div style={{
              width: `${Math.min(stats.progress_percentage, 100)}%`,
              height: '100%',
              backgroundColor: stats.progress_percentage >= 80 ? '#28a745' : stats.progress_percentage >= 50 ? '#ffc107' : '#dc3545',
              transition: 'width 0.5s ease'
            }}></div>
          </div>
          <p style={{ margin: '10px 0 0 0', fontSize: '14px', color: '#6c757d' }}>
            {stats.total_points} / {stats.target_angka_kredit} (Minimal: {stats.angka_kredit_minimal})
          </p>
        </div>
      )}

      {/* Stats Cards */}
      {stats && (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '20px', marginBottom: '30px' }}>
          <div style={{ padding: '20px', backgroundColor: '#f8f9fa', borderRadius: '8px', border: '1px solid #dee2e6' }}>
            <h3 style={{ margin: '0 0 10px 0', fontSize: '16px', color: '#6c757d' }}>Total Activities</h3>
            <p style={{ margin: 0, fontSize: '32px', fontWeight: 'bold', color: '#495057' }}>{stats.total_activities}</p>
          </div>

          <div style={{ padding: '20px', backgroundColor: '#fff3cd', borderRadius: '8px', border: '1px solid #ffc107' }}>
            <h3 style={{ margin: '0 0 10px 0', fontSize: '16px', color: '#856404' }}>Pending</h3>
            <p style={{ margin: 0, fontSize: '32px', fontWeight: 'bold', color: '#856404' }}>{stats.pending}</p>
          </div>

          <div style={{ padding: '20px', backgroundColor: '#d4edda', borderRadius: '8px', border: '1px solid #28a745' }}>
            <h3 style={{ margin: '0 0 10px 0', fontSize: '16px', color: '#155724' }}>Approved</h3>
            <p style={{ margin: 0, fontSize: '32px', fontWeight: 'bold', color: '#155724' }}>{stats.approved}</p>
          </div>

          <div style={{ padding: '20px', backgroundColor: '#f8d7da', borderRadius: '8px', border: '1px solid #dc3545' }}>
            <h3 style={{ margin: '0 0 10px 0', fontSize: '16px', color: '#721c24' }}>Rejected</h3>
            <p style={{ margin: 0, fontSize: '32px', fontWeight: 'bold', color: '#721c24' }}>{stats.rejected}</p>
          </div>

          <div style={{ padding: '20px', backgroundColor: '#d1ecf1', borderRadius: '8px', border: '1px solid #17a2b8' }}>
            <h3 style={{ margin: '0 0 10px 0', fontSize: '16px', color: '#0c5460' }}>Total Points</h3>
            <p style={{ margin: 0, fontSize: '32px', fontWeight: 'bold', color: '#0c5460' }}>{stats.total_points.toFixed(3)}</p>
          </div>
        </div>
      )}

      {/* Compliance Cards */}
      {stats && stats.total_points > 0 && (
        <div style={{ marginBottom: '30px' }}>
          <h2>Compliance Status (PR No. 3 Tahun 2025)</h2>
          <div style={{
            padding: '20px',
            backgroundColor: stats.is_compliant ? '#d4edda' : '#fff3cd',
            borderRadius: '8px',
            border: `2px solid ${stats.is_compliant ? '#28a745' : '#ffc107'}`,
            marginBottom: '20px'
          }}>
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
              <h3 style={{ margin: 0 }}>
                {stats.is_compliant ? '✅ Compliant' : '⚠️ Not Compliant'}
              </h3>
              <span style={{ fontSize: '14px', color: '#6c757d' }}>Pasal 3: Unsur Utama ≥80%, Penunjang ≤20%</span>
            </div>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))', gap: '20px' }}>
            <div style={{
              padding: '20px',
              backgroundColor: stats.utama_percentage >= 80 ? '#d4edda' : '#f8d7da',
              borderRadius: '8px',
              border: `1px solid ${stats.utama_percentage >= 80 ? '#28a745' : '#dc3545'}`
            }}>
              <h3 style={{ margin: '0 0 10px 0', fontSize: '16px', color: '#495057' }}>Unsur Utama</h3>
              <p style={{ margin: '0 0 5px 0', fontSize: '32px', fontWeight: 'bold', color: '#495057' }}>
                {stats.utama_percentage.toFixed(1)}%
              </p>
              <p style={{ margin: 0, fontSize: '14px', color: '#6c757d' }}>
                {stats.utama_points.toFixed(3)} points
              </p>
            </div>

            <div style={{
              padding: '20px',
              backgroundColor: stats.penunjang_percentage <= 20 ? '#d4edda' : '#f8d7da',
              borderRadius: '8px',
              border: `1px solid ${stats.penunjang_percentage <= 20 ? '#28a745' : '#dc3545'}`
            }}>
              <h3 style={{ margin: '0 0 10px 0', fontSize: '16px', color: '#495057' }}>Unsur Penunjang</h3>
              <p style={{ margin: '0 0 5px 0', fontSize: '32px', fontWeight: 'bold', color: '#495057' }}>
                {stats.penunjang_percentage.toFixed(1)}%
              </p>
              <p style={{ margin: 0, fontSize: '14px', color: '#6c757d' }}>
                {stats.penunjang_points.toFixed(3)} points
              </p>
            </div>
          </div>
        </div>
      )}

      {/* Category Summary */}
      <div style={{ marginBottom: '30px' }}>
        <h2>Summary by Category</h2>
        {summary.length > 0 ? (
          <table style={{ width: '100%', borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ backgroundColor: '#f8f9fa' }}>
                <th style={{ padding: '12px', textAlign: 'left', borderBottom: '2px solid #dee2e6' }}>Category</th>
                <th style={{ padding: '12px', textAlign: 'right', borderBottom: '2px solid #dee2e6' }}>Total Activities</th>
                <th style={{ padding: '12px', textAlign: 'right', borderBottom: '2px solid #dee2e6' }}>Approved</th>
                <th style={{ padding: '12px', textAlign: 'right', borderBottom: '2px solid #dee2e6' }}>Earned Points</th>
              </tr>
            </thead>
            <tbody>
              {summary.map((item, index) => (
                <tr key={index} style={{ borderBottom: '1px solid #dee2e6' }}>
                  <td style={{ padding: '12px' }}>{item.category}</td>
                  <td style={{ padding: '12px', textAlign: 'right' }}>{item.total_activities}</td>
                  <td style={{ padding: '12px', textAlign: 'right' }}>{item.approved_count}</td>
                  <td style={{ padding: '12px', textAlign: 'right', fontWeight: 'bold' }}>{item.earned_points}</td>
                </tr>
              ))}
            </tbody>
          </table>
        ) : (
          <p>No activities yet. Start by submitting your first activity!</p>
        )}
      </div>

      {/* Quick Actions */}
      <div>
        <h2>Quick Actions</h2>
        <button
          onClick={() => navigate('/activities')}
          style={{
            padding: '12px 24px',
            backgroundColor: '#007bff',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer',
            marginRight: '10px'
          }}
        >
          View My Activities
        </button>
        <button
          onClick={() => navigate('/activities/new')}
          style={{
            padding: '12px 24px',
            backgroundColor: '#28a745',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer'
          }}
        >
          Submit New Activity
        </button>
      </div>
    </div>
  );
};

export default DashboardPage;
