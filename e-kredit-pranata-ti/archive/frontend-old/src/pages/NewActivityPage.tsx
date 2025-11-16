import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { activityService } from '../services/activityService';
import { creditSchemaService } from '../services/creditSchemaService';
import { CreditSchema } from '../types';
import { useAuth } from '../contexts/AuthContext';

const NewActivityPage: React.FC = () => {
  const navigate = useNavigate();
  const { user } = useAuth();
  const [schemas, setSchemas] = useState<CreditSchema[]>([]);
  const [categories, setCategories] = useState<string[]>([]);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  // Form fields
  const [selectedCategory, setSelectedCategory] = useState('');
  const [schemaId, setSchemaId] = useState('');
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [proofFile, setProofFile] = useState<File | null>(null);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setLoading(true);
      const [schemasData, categoriesData] = await Promise.all([
        creditSchemaService.getSchemas(undefined, false), // Get all schemas without pagination
        creditSchemaService.getCategories(),
      ]);
      setSchemas(schemasData);
      setCategories(categoriesData);
    } catch (err: any) {
      setError('Failed to load credit schemas');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const filteredSchemas = selectedCategory
    ? schemas.filter(s => s.category === selectedCategory)
    : schemas;

  const selectedSchema = schemas.find(s => s.id === parseInt(schemaId));

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setSubmitting(true);

    try {
      const formData = new FormData();
      formData.append('schema_id', schemaId);
      formData.append('title', title);
      formData.append('description', description);
      if (proofFile) {
        formData.append('proof_file', proofFile);
      }

      await activityService.createActivity(formData);
      alert('Activity submitted successfully!');
      navigate('/activities');
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to submit activity');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return <div style={{ padding: '20px' }}>Loading...</div>;
  }

  return (
    <div style={{ padding: '20px', maxWidth: '800px', margin: '0 auto' }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
        <h1>Submit New Activity</h1>
        <button
          onClick={() => navigate('/dashboard')}
          style={{
            padding: '8px 16px',
            backgroundColor: '#6c757d',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer'
          }}
        >
          Back to Dashboard
        </button>
      </div>

      {user?.jenjang_jabatan && (
        <div style={{ padding: '15px', backgroundColor: '#e7f3ff', borderRadius: '4px', marginBottom: '20px', border: '1px solid #b3d9ff' }}>
          <p style={{ margin: 0, fontSize: '14px' }}>
            <strong>Your Jenjang:</strong> {user.jenjang_jabatan} ({user.golongan})
          </p>
          <p style={{ margin: '5px 0 0 0', fontSize: '12px', color: '#6c757d' }}>
            Some activities may be restricted based on your jenjang level
          </p>
        </div>
      )}

      {error && (
        <div style={{ color: 'red', padding: '10px', marginBottom: '20px', border: '1px solid red', borderRadius: '4px' }}>
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit}>
        {/* Category Filter */}
        <div style={{ marginBottom: '20px' }}>
          <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>
            Filter by Category:
          </label>
          <select
            value={selectedCategory}
            onChange={(e) => {
              setSelectedCategory(e.target.value);
              setSchemaId(''); // Reset schema selection when category changes
            }}
            style={{ width: '100%', padding: '8px', fontSize: '16px' }}
          >
            <option value="">All Categories</option>
            {categories.map((cat) => (
              <option key={cat} value={cat}>{cat}</option>
            ))}
          </select>
        </div>

        {/* Credit Schema Selection */}
        <div style={{ marginBottom: '20px' }}>
          <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>
            Select Activity Type: <span style={{ color: 'red' }}>*</span>
          </label>
          <select
            value={schemaId}
            onChange={(e) => setSchemaId(e.target.value)}
            required
            style={{ width: '100%', padding: '8px', fontSize: '16px' }}
          >
            <option value="">-- Select Activity --</option>
            {filteredSchemas.map((schema) => (
              <option key={schema.id} value={schema.id}>
                {schema.activity_name} ({schema.credit_points} points)
                {schema.pelaksana && schema.pelaksana !== 'Semua Jenjang' ? ` - ${schema.pelaksana}` : ''}
              </option>
            ))}
          </select>
        </div>

        {/* Schema Details */}
        {selectedSchema && (
          <div style={{ padding: '15px', backgroundColor: '#f8f9fa', borderRadius: '4px', marginBottom: '20px' }}>
            <h3 style={{ margin: '0 0 10px 0' }}>Activity Details:</h3>
            <table style={{ width: '100%', fontSize: '14px' }}>
              <tbody>
                <tr>
                  <td style={{ padding: '5px', fontWeight: 'bold', width: '150px' }}>Category:</td>
                  <td style={{ padding: '5px' }}>{selectedSchema.category}</td>
                </tr>
                {selectedSchema.subcategory && (
                  <tr>
                    <td style={{ padding: '5px', fontWeight: 'bold' }}>Subcategory:</td>
                    <td style={{ padding: '5px' }}>{selectedSchema.subcategory}</td>
                  </tr>
                )}
                <tr>
                  <td style={{ padding: '5px', fontWeight: 'bold' }}>Credit Points:</td>
                  <td style={{ padding: '5px' }}>{selectedSchema.credit_points}</td>
                </tr>
                {selectedSchema.satuan_hasil && (
                  <tr>
                    <td style={{ padding: '5px', fontWeight: 'bold' }}>Unit:</td>
                    <td style={{ padding: '5px' }}>{selectedSchema.satuan_hasil}</td>
                  </tr>
                )}
                {selectedSchema.pelaksana && (
                  <tr>
                    <td style={{ padding: '5px', fontWeight: 'bold' }}>Level:</td>
                    <td style={{ padding: '5px' }}>{selectedSchema.pelaksana}</td>
                  </tr>
                )}
                {selectedSchema.batasan_penilaian && (
                  <tr>
                    <td style={{ padding: '5px', fontWeight: 'bold' }}>Limitation:</td>
                    <td style={{ padding: '5px' }}>{selectedSchema.batasan_penilaian}</td>
                  </tr>
                )}
                {selectedSchema.bukti_fisik && (
                  <tr>
                    <td style={{ padding: '5px', fontWeight: 'bold' }}>Required Proof:</td>
                    <td style={{ padding: '5px' }}>{selectedSchema.bukti_fisik}</td>
                  </tr>
                )}
                <tr>
                  <td style={{ padding: '5px', fontWeight: 'bold' }}>Type:</td>
                  <td style={{ padding: '5px' }}>
                    <span style={{
                      padding: '2px 8px',
                      backgroundColor: selectedSchema.unsur_type === 'utama' ? '#d4edda' : '#fff3cd',
                      color: selectedSchema.unsur_type === 'utama' ? '#155724' : '#856404',
                      borderRadius: '4px',
                      fontSize: '12px',
                      fontWeight: 'bold'
                    }}>
                      {selectedSchema.unsur_type === 'utama' ? 'Unsur Utama' : 'Unsur Penunjang'}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
            {selectedSchema.description && (
              <p style={{ margin: '10px 0 0 0', fontSize: '13px', color: '#6c757d' }}>
                {selectedSchema.description}
              </p>
            )}
          </div>
        )}

        {/* Title */}
        <div style={{ marginBottom: '20px' }}>
          <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>
            Activity Title: <span style={{ color: 'red' }}>*</span>
          </label>
          <input
            type="text"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            required
            placeholder="e.g., Implementasi sistem manajemen..."
            style={{ width: '100%', padding: '8px', fontSize: '16px' }}
          />
        </div>

        {/* Description */}
        <div style={{ marginBottom: '20px' }}>
          <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>
            Description: <span style={{ color: 'red' }}>*</span>
          </label>
          <textarea
            value={description}
            onChange={(e) => setDescription(e.target.value)}
            required
            rows={6}
            placeholder="Describe your activity in detail..."
            style={{ width: '100%', padding: '8px', fontSize: '16px', fontFamily: 'inherit' }}
          />
        </div>

        {/* File Upload */}
        <div style={{ marginBottom: '20px' }}>
          <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>
            Proof File (optional):
          </label>
          <input
            type="file"
            accept=".pdf,.jpg,.jpeg,.png"
            onChange={(e) => setProofFile(e.target.files?.[0] || null)}
            style={{ width: '100%', padding: '8px', fontSize: '16px' }}
          />
          <p style={{ margin: '5px 0 0 0', fontSize: '12px', color: '#6c757d' }}>
            Accepted formats: PDF, JPG, PNG (max 5MB)
          </p>
        </div>

        {/* Submit Buttons */}
        <div style={{ display: 'flex', gap: '10px' }}>
          <button
            type="submit"
            disabled={submitting}
            style={{
              padding: '12px 24px',
              backgroundColor: submitting ? '#6c757d' : '#28a745',
              color: 'white',
              border: 'none',
              borderRadius: '4px',
              cursor: submitting ? 'not-allowed' : 'pointer',
              fontSize: '16px'
            }}
          >
            {submitting ? 'Submitting...' : 'Submit Activity'}
          </button>
          <button
            type="button"
            onClick={() => navigate('/activities')}
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
            Cancel
          </button>
        </div>
      </form>
    </div>
  );
};

export default NewActivityPage;
