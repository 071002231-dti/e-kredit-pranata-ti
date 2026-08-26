import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider } from './contexts/AuthContext'
import { ProtectedRoute } from './components/ProtectedRoute'
import { LoginPage } from './pages/LoginPage'
import { RegisterPage } from './pages/RegisterPage'
import { SsoCallbackPage } from './pages/SsoCallbackPage'
import { DashboardPage } from './pages/DashboardPage'
import { ActivitiesPage } from './pages/ActivitiesPage'
import { ActivityFormPage } from './pages/ActivityFormPage'
import { CreditBankPage } from './pages/CreditBankPage'
import { SkpListPage } from './pages/SkpListPage'
import { SkpFormPage } from './pages/SkpFormPage'
import { ApprovalsPage } from './pages/ApprovalsPage'
import { UsersPage } from './pages/UsersPage'
import { SchemasPage } from './pages/SchemasPage'

// Get basename from Vite's base config (production: /ccp, development: /)
const basename = import.meta.env.BASE_URL

function App() {
  return (
    <BrowserRouter basename={basename}>
      <AuthProvider>
        <Routes>
          {/* Public routes */}
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route path="/auth/sso/callback" element={<SsoCallbackPage />} />

          {/* Protected routes */}
          <Route
            path="/dashboard"
            element={
              <ProtectedRoute>
                <DashboardPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/activities"
            element={
              <ProtectedRoute>
                <ActivitiesPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/activities/new"
            element={
              <ProtectedRoute>
                <ActivityFormPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/activities/:id/edit"
            element={
              <ProtectedRoute>
                <ActivityFormPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/credit-banks"
            element={
              <ProtectedRoute>
                <CreditBankPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/skp"
            element={
              <ProtectedRoute>
                <SkpListPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/skp/new"
            element={
              <ProtectedRoute>
                <SkpFormPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/skp/:id"
            element={
              <ProtectedRoute>
                <SkpFormPage />
              </ProtectedRoute>
            }
          />

          {/* Verifier & Admin routes */}
          <Route
            path="/approvals"
            element={
              <ProtectedRoute>
                <ApprovalsPage />
              </ProtectedRoute>
            }
          />

          {/* Admin only routes */}
          <Route
            path="/admin/schemas"
            element={
              <ProtectedRoute>
                <SchemasPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/users"
            element={
              <ProtectedRoute>
                <UsersPage />
              </ProtectedRoute>
            }
          />

          {/* Default redirect */}
          <Route path="/" element={<Navigate to="/dashboard" replace />} />
          <Route path="*" element={<Navigate to="/dashboard" replace />} />
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  )
}

export default App
