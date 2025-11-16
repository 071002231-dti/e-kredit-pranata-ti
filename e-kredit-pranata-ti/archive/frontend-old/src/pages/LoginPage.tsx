import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

const LoginPage: React.FC = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      await login({ email, password });
      navigate('/dashboard');
    } catch (err: any) {
      setError(err.response?.data?.message || 'Login failed. Please check your credentials.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ maxWidth: '400px', margin: '50px auto', padding: '20px' }}>
      <h1>Login - e-Kredit Pranata TI</h1>

      {error && (
        <div style={{ color: 'red', padding: '10px', marginBottom: '20px', border: '1px solid red', borderRadius: '4px' }}>
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit}>
        <div style={{ marginBottom: '15px' }}>
          <label style={{ display: 'block', marginBottom: '5px' }}>Email:</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            style={{ width: '100%', padding: '8px', fontSize: '16px' }}
          />
        </div>

        <div style={{ marginBottom: '15px' }}>
          <label style={{ display: 'block', marginBottom: '5px' }}>Password:</label>
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            style={{ width: '100%', padding: '8px', fontSize: '16px' }}
          />
        </div>

        <button
          type="submit"
          disabled={loading}
          style={{
            width: '100%',
            padding: '10px',
            fontSize: '16px',
            backgroundColor: '#007bff',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: loading ? 'not-allowed' : 'pointer'
          }}
        >
          {loading ? 'Logging in...' : 'Login'}
        </button>
      </form>

      <div style={{ marginTop: '20px', textAlign: 'center' }}>
        <p>Don't have an account? <Link to="/register">Register here</Link></p>
      </div>

      <div style={{ marginTop: '30px', padding: '15px', backgroundColor: '#f8f9fa', borderRadius: '4px' }}>
        <h3 style={{ marginBottom: '15px' }}>Test Users (PR No. 3 Th 2025 Compliant):</h3>
        <table style={{ width: '100%', fontSize: '13px', borderCollapse: 'collapse' }}>
          <thead>
            <tr style={{ backgroundColor: '#e9ecef' }}>
              <th style={{ padding: '8px', textAlign: 'left', borderBottom: '2px solid #dee2e6' }}>Email</th>
              <th style={{ padding: '8px', textAlign: 'left', borderBottom: '2px solid #dee2e6' }}>Jenjang</th>
              <th style={{ padding: '8px', textAlign: 'left', borderBottom: '2px solid #dee2e6' }}>Gol</th>
              <th style={{ padding: '8px', textAlign: 'right', borderBottom: '2px solid #dee2e6' }}>Target</th>
            </tr>
          </thead>
          <tbody>
            <tr style={{ borderBottom: '1px solid #dee2e6' }}>
              <td style={{ padding: '8px' }}>user@example.com</td>
              <td style={{ padding: '8px' }}>PTI Muda</td>
              <td style={{ padding: '8px' }}>III/d</td>
              <td style={{ padding: '8px', textAlign: 'right' }}>300</td>
            </tr>
            <tr style={{ borderBottom: '1px solid #dee2e6' }}>
              <td style={{ padding: '8px' }}>pertama@example.com</td>
              <td style={{ padding: '8px' }}>PTI Pertama</td>
              <td style={{ padding: '8px' }}>III/a</td>
              <td style={{ padding: '8px', textAlign: 'right' }}>100</td>
            </tr>
            <tr style={{ borderBottom: '1px solid #dee2e6' }}>
              <td style={{ padding: '8px' }}>pelaksana@example.com</td>
              <td style={{ padding: '8px' }}>PTI Pelaksana</td>
              <td style={{ padding: '8px' }}>II/b</td>
              <td style={{ padding: '8px', textAlign: 'right' }}>40</td>
            </tr>
            <tr style={{ borderBottom: '1px solid #dee2e6' }}>
              <td style={{ padding: '8px' }}>verifier@example.com</td>
              <td style={{ padding: '8px' }}>PTI Madya</td>
              <td style={{ padding: '8px' }}>IV/c</td>
              <td style={{ padding: '8px', textAlign: 'right' }}>700</td>
            </tr>
            <tr>
              <td style={{ padding: '8px' }}>admin@example.com</td>
              <td style={{ padding: '8px' }}>PTI Utama</td>
              <td style={{ padding: '8px' }}>IV/e</td>
              <td style={{ padding: '8px', textAlign: 'right' }}>1050</td>
            </tr>
          </tbody>
        </table>
        <p style={{ fontSize: '12px', color: '#6c757d', marginTop: '10px', marginBottom: 0 }}>
          Password for all: <strong>password</strong>
        </p>
      </div>
    </div>
  );
};

export default LoginPage;
