import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { activityService } from '../services/activityService';
import { Activity } from '../types';

const ActivityDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [activity, setActivity] = useState<Activity | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    loadActivity();
  }, [id]);

  const loadActivity = async () => {
    if (!id) return;

    try {
      setLoading(true);
      const data = await activityService.getActivity(parseInt(id));
      setActivity(data);
    } catch (err: any) {
      setError('Failed to load activity details');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const getStatusColor = (status: string) => {
    const colors: any = {
      pending: { bg: '#fff3cd', color: '#856404', border: '#ffc107' },
      approved: { bg: '#d4edda', color: '#155724', border: '#28a745' },
      rejected: { bg: '#f8d7da', color: '#721c24', border: '#dc3545' },
    };
    return colors[status] || colors.pending;
  };

  if (loading) {
    return <div style={{ padding: '20px' }}>Loading activity details...</div>;
  }

  if (error || !activity) {
    return (
      <div style={{ padding: '20px' }}>
        <div style={{ color: 'red', marginBottom: '20px' }}>{error || 'Activity not found'}</div>
        <button
          onClick={() => navigate('/activities')}
          style={{
            padding: '10px 20px',
            backgroundColor: '#007bff',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer'
          }}
        >
          Back to Activities
        </button>
      </div>
    );
  }

  const statusColor = getStatusColor(activity.status);

  return (
    <div style={{ padding: '20px', maxWidth: '900px', margin: '0 auto' }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
        <h1>Activity Details</h1>
        <button
          onClick={() => navigate('/activities')}
          style={{
            padding: '8px 16px',
            backgroundColor: '#6c757d',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer'
          }}
        >
          Back to Activities
        </button>
      </div>

      {/* Status Badge */}
      <div style={{ marginBottom: '30px' }}>
        <span style={{
          padding: '8px 16px',
          backgroundColor: statusColor.bg,
          color: statusColor.color,
          border: `2px solid ${statusColor.border}`,
          borderRadius: '8px',
          fontSize: '16px',
          fontWeight: 'bold',
          textTransform: 'uppercase',
          display: 'inline-block'
        }}>
          {activity.status}
        </span>
      </div>

      {/* Activity Information */}
      <div style={{ marginBottom: '30px', padding: '20px', backgroundColor: '#f8f9fa', borderRadius: '8px' }}>
        <h2 style={{ marginTop: 0 }}>Activity Information</h2>
        <table style={{ width: '100%', fontSize: '15px' }}>
          <tbody>
            <tr style={{ borderBottom: '1px solid #dee2e6' }}>
              <td style={{ padding: '12px', fontWeight: 'bold', width: '200px' }}>Title:</td>
              <td style={{ padding: '12px' }}>{activity.title}</td>
            </tr>
            <tr style={{ borderBottom: '1px solid #dee2e6' }}>
              <td style={{ padding: '12px', fontWeight: 'bold' }}>Description:</td>
              <td style={{ padding: '12px', whiteSpace: 'pre-wrap' }}>{activity.description}</td>
            </tr>
            <tr style={{ borderBottom: '1px solid #dee2e6' }}>
              <td style={{ padding: '12px', fontWeight: 'bold' }}>Submitted At:</td>
              <td style={{ padding: '12px' }}>
                {new Date(activity.submitted_at).toLocaleString()}
              </td>
            </tr>
            {activity.proof_file && (
              <tr style={{ borderBottom: '1px solid #dee2e6' }}>
                <td style={{ padding: '12px', fontWeight: 'bold' }}>Proof File:</td>
                <td style={{ padding: '12px' }}>
                  <a
                    href={`http://localhost/storage/${activity.proof_file}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    style={{ color: '#007bff', textDecoration: 'underline' }}
                  >
                    View File
                  </a>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Credit Schema Information */}
      {activity.credit_schema && (
        <div style={{ marginBottom: '30px', padding: '20px', backgroundColor: '#e7f3ff', borderRadius: '8px', border: '1px solid #b3d9ff' }}>
          <h2 style={{ marginTop: 0 }}>Credit Schema Details</h2>
          <table style={{ width: '100%', fontSize: '15px' }}>
            <tbody>
              <tr style={{ borderBottom: '1px solid #b3d9ff' }}>
                <td style={{ padding: '12px', fontWeight: 'bold', width: '200px' }}>Activity Name:</td>
                <td style={{ padding: '12px' }}>{activity.credit_schema.activity_name}</td>
              </tr>
              <tr style={{ borderBottom: '1px solid #b3d9ff' }}>
                <td style={{ padding: '12px', fontWeight: 'bold' }}>Category:</td>
                <td style={{ padding: '12px' }}>{activity.credit_schema.category}</td>
              </tr>
              {activity.credit_schema.subcategory && (
                <tr style={{ borderBottom: '1px solid #b3d9ff' }}>
                  <td style={{ padding: '12px', fontWeight: 'bold' }}>Subcategory:</td>
                  <td style={{ padding: '12px' }}>{activity.credit_schema.subcategory}</td>
                </tr>
              )}
              <tr style={{ borderBottom: '1px solid #b3d9ff' }}>
                <td style={{ padding: '12px', fontWeight: 'bold' }}>Credit Points:</td>
                <td style={{ padding: '12px' }}>
                  <span style={{ fontSize: '20px', fontWeight: 'bold', color: '#007bff' }}>
                    {activity.credit_schema.credit_points}
                  </span>
                </td>
              </tr>
              {activity.credit_schema.satuan_hasil && (
                <tr style={{ borderBottom: '1px solid #b3d9ff' }}>
                  <td style={{ padding: '12px', fontWeight: 'bold' }}>Unit (Satuan Hasil):</td>
                  <td style={{ padding: '12px' }}>{activity.credit_schema.satuan_hasil}</td>
                </tr>
              )}
              {activity.credit_schema.pelaksana && (
                <tr style={{ borderBottom: '1px solid #b3d9ff' }}>
                  <td style={{ padding: '12px', fontWeight: 'bold' }}>Level (Pelaksana):</td>
                  <td style={{ padding: '12px' }}>{activity.credit_schema.pelaksana}</td>
                </tr>
              )}
              {activity.credit_schema.batasan_penilaian && (
                <tr style={{ borderBottom: '1px solid #b3d9ff' }}>
                  <td style={{ padding: '12px', fontWeight: 'bold' }}>Limitation:</td>
                  <td style={{ padding: '12px' }}>{activity.credit_schema.batasan_penilaian}</td>
                </tr>
              )}
              {activity.credit_schema.bukti_fisik && (
                <tr style={{ borderBottom: '1px solid #b3d9ff' }}>
                  <td style={{ padding: '12px', fontWeight: 'bold' }}>Required Proof:</td>
                  <td style={{ padding: '12px' }}>{activity.credit_schema.bukti_fisik}</td>
                </tr>
              )}
              {activity.credit_schema.unsur_type && (
                <tr>
                  <td style={{ padding: '12px', fontWeight: 'bold' }}>Type:</td>
                  <td style={{ padding: '12px' }}>
                    <span style={{
                      padding: '4px 12px',
                      backgroundColor: activity.credit_schema.unsur_type === 'utama' ? '#d4edda' : '#fff3cd',
                      color: activity.credit_schema.unsur_type === 'utama' ? '#155724' : '#856404',
                      borderRadius: '4px',
                      fontSize: '14px',
                      fontWeight: 'bold'
                    }}>
                      {activity.credit_schema.unsur_type === 'utama' ? 'Unsur Utama' : 'Unsur Penunjang'}
                    </span>
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}

      {/* Approval History */}
      {activity.approvals && activity.approvals.length > 0 && (
        <div style={{ marginBottom: '30px' }}>
          <h2>Approval History</h2>
          {activity.approvals.map((approval, index) => (
            <div
              key={approval.id}
              style={{
                padding: '15px',
                backgroundColor: approval.status === 'approved' ? '#d4edda' : '#f8d7da',
                border: `1px solid ${approval.status === 'approved' ? '#28a745' : '#dc3545'}`,
                borderRadius: '4px',
                marginBottom: '10px'
              }}
            >
              <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '10px' }}>
                <strong>{approval.status === 'approved' ? '✓ Approved' : '✗ Rejected'}</strong>
                <span style={{ fontSize: '14px', color: '#6c757d' }}>
                  {new Date(approval.approved_at).toLocaleString()}
                </span>
              </div>
              {approval.verifier && (
                <p style={{ margin: '5px 0', fontSize: '14px' }}>
                  <strong>Verifier:</strong> {approval.verifier.name} ({approval.verifier.email})
                </p>
              )}
              {approval.comments && (
                <p style={{ margin: '10px 0 0 0', fontSize: '14px' }}>
                  <strong>Comments:</strong> {approval.comments}
                </p>
              )}
            </div>
          ))}
        </div>
      )}

      {/* Actions */}
      <div style={{ display: 'flex', gap: '10px' }}>
        <button
          onClick={() => navigate('/activities')}
          style={{
            padding: '12px 24px',
            backgroundColor: '#007bff',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer',
            fontSize: '16px'
          }}
        >
          Back to Activities
        </button>
        <button
          onClick={() => navigate('/dashboard')}
          style={{
            padding: '12px 24px',
            backgroundColor: '#6c757d',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer',
            fontSize: '16px'
          }}
        >
          Go to Dashboard
        </button>
      </div>
    </div>
  );
};

export default ActivityDetailPage;
