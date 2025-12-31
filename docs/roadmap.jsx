import React, { useState } from 'react';
import { CheckCircle2, Circle, Clock, AlertCircle, Zap, Users, Database, Smartphone, Rocket, Settings, TrendingUp } from 'lucide-react';

const ProjectRoadmap = () => {
  const [selectedPhase, setSelectedPhase] = useState(null);
  const [completedTasks, setCompletedTasks] = useState(new Set());

  const phases = [
    {
      id: 'phase3',
      name: 'Phase 3: Critical Fixes & Stabilization',
      status: 'current',
      priority: 'critical',
      duration: '1 week',
      startDate: 'Dec 26, 2025',
      icon: AlertCircle,
      color: 'red',
      progress: 0,
      tasks: [
        {
          id: 'p3-1',
          title: 'Fix Dashboard Display Issue',
          description: 'Debug API response serialization & frontend data binding untuk jenjang_jabatan, golongan, target_angka_kredit',
          priority: 'critical',
          estimation: '4-6 hours',
          dependencies: []
        },
        {
          id: 'p3-2',
          title: 'Verify ComplianceService Bug Fixes',
          description: 'Test ComplianceService setelah fix undefined array key di line 180, 186',
          priority: 'high',
          estimation: '2-3 hours',
          dependencies: ['p3-1']
        },
        {
          id: 'p3-3',
          title: 'Complete Credit Schema Seeder Validation',
          description: 'Manual verification dengan PDF PR No. 3/2025 halaman 10-16',
          priority: 'high',
          estimation: '3-4 hours',
          dependencies: []
        },
        {
          id: 'p3-4',
          title: 'Database Integrity Check',
          description: 'Verify 28 comprehensive users, all 27 golongan combinations, target AK values',
          priority: 'medium',
          estimation: '2 hours',
          dependencies: ['p3-3']
        }
      ]
    },
    {
      id: 'phase4',
      name: 'Phase 4: Testing & Quality Assurance',
      status: 'upcoming',
      priority: 'high',
      duration: '1-2 weeks',
      startDate: 'Jan 2, 2026',
      icon: CheckCircle2,
      color: 'blue',
      progress: 0,
      tasks: [
        {
          id: 'p4-1',
          title: 'End-to-End Flow Testing',
          description: 'Test complete user journey: register → submit activity → approval → credit calculation → banking',
          priority: 'critical',
          estimation: '1 day',
          dependencies: []
        },
        {
          id: 'p4-2',
          title: 'Credit Banking Scenarios',
          description: 'Test compliance violations, max credits, 80/20 rule, banking & unlock scenarios',
          priority: 'high',
          estimation: '1 day',
          dependencies: ['p4-1']
        },
        {
          id: 'p4-3',
          title: 'Multi-User Role Testing',
          description: 'Test sebagai PTI user, verifier, dan admin dengan berbagai jenjang',
          priority: 'high',
          estimation: '1 day',
          dependencies: ['p4-1']
        },
        {
          id: 'p4-4',
          title: 'API Performance Testing',
          description: 'Load testing untuk dashboard, activities list, compliance calculations',
          priority: 'medium',
          estimation: '4-6 hours',
          dependencies: ['p4-1', 'p4-2']
        },
        {
          id: 'p4-5',
          title: 'Frontend Cross-Browser Testing',
          description: 'Test di Chrome, Firefox, Safari, Edge - desktop & mobile',
          priority: 'medium',
          estimation: '4 hours',
          dependencies: ['p4-1']
        },
        {
          id: 'p4-6',
          title: 'Security Audit',
          description: 'Check authentication, authorization, SQL injection, XSS vulnerabilities',
          priority: 'high',
          estimation: '1 day',
          dependencies: []
        }
      ]
    },
    {
      id: 'phase5',
      name: 'Phase 5: WhatsApp Production Setup',
      status: 'upcoming',
      priority: 'high',
      duration: '1 week',
      startDate: 'Jan 13, 2026',
      icon: Smartphone,
      color: 'green',
      progress: 0,
      tasks: [
        {
          id: 'p5-1',
          title: 'Meta Business Account Setup',
          description: 'Create & configure Meta Business account, WhatsApp Business API access',
          priority: 'critical',
          estimation: '1 day',
          dependencies: []
        },
        {
          id: 'p5-2',
          title: 'Production Webhook Configuration',
          description: 'Setup production domain, configure webhook URL, verify webhook',
          priority: 'critical',
          estimation: '4 hours',
          dependencies: ['p5-1']
        },
        {
          id: 'p5-3',
          title: 'WhatsApp Flow Publishing',
          description: 'Publish WhatsApp Flows for production use, test interactive forms',
          priority: 'high',
          estimation: '1 day',
          dependencies: ['p5-1', 'p5-2']
        },
        {
          id: 'p5-4',
          title: 'WhatsApp Integration Testing',
          description: 'Test /register, /submit, /stats, /activities commands end-to-end',
          priority: 'critical',
          estimation: '1 day',
          dependencies: ['p5-2', 'p5-3']
        },
        {
          id: 'p5-5',
          title: 'Notification System Testing',
          description: 'Test approval/rejection notifications, verify delivery',
          priority: 'high',
          estimation: '4 hours',
          dependencies: ['p5-4']
        }
      ]
    },
    {
      id: 'phase6',
      name: 'Phase 6: Deployment & Go Live',
      status: 'upcoming',
      priority: 'high',
      duration: '1 week',
      startDate: 'Jan 20, 2026',
      icon: Rocket,
      color: 'purple',
      progress: 0,
      tasks: [
        {
          id: 'p6-1',
          title: 'Production Server Setup',
          description: 'Setup VPS, configure Nginx, SSL certificates, domain configuration',
          priority: 'critical',
          estimation: '1 day',
          dependencies: []
        },
        {
          id: 'p6-2',
          title: 'Database Migration Production',
          description: 'Run migrations, seed credit schemas, create admin users',
          priority: 'critical',
          estimation: '4 hours',
          dependencies: ['p6-1']
        },
        {
          id: 'p6-3',
          title: 'Docker Production Configuration',
          description: 'Configure docker-compose for production, environment variables, secrets',
          priority: 'high',
          estimation: '4-6 hours',
          dependencies: ['p6-1']
        },
        {
          id: 'p6-4',
          title: 'CI/CD Pipeline Setup',
          description: 'GitHub Actions for automated testing & deployment',
          priority: 'medium',
          estimation: '1 day',
          dependencies: ['p6-1', 'p6-3']
        },
        {
          id: 'p6-5',
          title: 'Monitoring & Logging Setup',
          description: 'Setup Laravel logs, error tracking, performance monitoring',
          priority: 'high',
          estimation: '4 hours',
          dependencies: ['p6-1']
        },
        {
          id: 'p6-6',
          title: 'Backup Strategy Implementation',
          description: 'Daily database backup, file backup, backup restoration testing',
          priority: 'high',
          estimation: '4 hours',
          dependencies: ['p6-2']
        },
        {
          id: 'p6-7',
          title: 'Production Smoke Testing',
          description: 'Final testing di production environment sebelum launch',
          priority: 'critical',
          estimation: '1 day',
          dependencies: ['p6-2', 'p6-3', 'p6-5']
        }
      ]
    },
    {
      id: 'phase7',
      name: 'Phase 7: Post-Launch & Enhancements',
      status: 'future',
      priority: 'medium',
      duration: 'Ongoing',
      startDate: 'Feb 2026',
      icon: TrendingUp,
      color: 'indigo',
      progress: 0,
      tasks: [
        {
          id: 'p7-1',
          title: 'User Training & Documentation',
          description: 'Create user manual, video tutorials, conduct training sessions',
          priority: 'high',
          estimation: '1 week',
          dependencies: []
        },
        {
          id: 'p7-2',
          title: 'File Upload in WhatsApp Flows',
          description: 'Implement file/image upload capability in WhatsApp submissions',
          priority: 'medium',
          estimation: '1 week',
          dependencies: []
        },
        {
          id: 'p7-3',
          title: 'Verifier Approval via WhatsApp',
          description: 'Allow verifiers to approve/reject activities through WhatsApp',
          priority: 'medium',
          estimation: '1 week',
          dependencies: []
        },
        {
          id: 'p7-4',
          title: 'Advanced Analytics Dashboard',
          description: 'Enhanced analytics, charts, trends, comparative analysis',
          priority: 'low',
          estimation: '2 weeks',
          dependencies: []
        },
        {
          id: 'p7-5',
          title: 'Export Reports (PDF/Excel)',
          description: 'Generate PDF certificates, Excel reports for activities & SKP',
          priority: 'medium',
          estimation: '1 week',
          dependencies: []
        },
        {
          id: 'p7-6',
          title: 'Multi-language Support',
          description: 'Add English language option (ID/EN toggle)',
          priority: 'low',
          estimation: '1 week',
          dependencies: []
        },
        {
          id: 'p7-7',
          title: 'Mobile App Development',
          description: 'React Native app for iOS & Android',
          priority: 'low',
          estimation: '2-3 months',
          dependencies: []
        }
      ]
    }
  ];

  const toggleTask = (taskId) => {
    const newCompleted = new Set(completedTasks);
    if (newCompleted.has(taskId)) {
      newCompleted.delete(taskId);
    } else {
      newCompleted.add(taskId);
    }
    setCompletedTasks(newCompleted);
  };

  const getPhaseProgress = (phase) => {
    const totalTasks = phase.tasks.length;
    const completed = phase.tasks.filter(t => completedTasks.has(t.id)).length;
    return Math.round((completed / totalTasks) * 100);
  };

  const priorityColors = {
    critical: 'bg-red-100 text-red-800 border-red-300',
    high: 'bg-orange-100 text-orange-800 border-orange-300',
    medium: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    low: 'bg-blue-100 text-blue-800 border-blue-300'
  };

  const statusColors = {
    current: 'bg-green-500',
    upcoming: 'bg-blue-500',
    future: 'bg-gray-400'
  };

  const iconColors = {
    red: 'text-red-600',
    blue: 'text-blue-600',
    green: 'text-green-600',
    purple: 'text-purple-600',
    indigo: 'text-indigo-600'
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="bg-white rounded-xl shadow-lg p-8 mb-6">
          <div className="flex items-start justify-between">
            <div>
              <h1 className="text-4xl font-bold text-slate-800 mb-2">
                🚀 e-Kredit Pranata TI Development Roadmap
              </h1>
              <p className="text-slate-600 text-lg">
                Laravel 12 + React 19 + WhatsApp Integration
              </p>
              <div className="flex gap-4 mt-4">
                <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                  Current Progress: ~70%
                </span>
                <span className="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                  Phase 2B Complete
                </span>
              </div>
            </div>
            <div className="text-right">
              <div className="text-sm text-slate-500">Last Updated</div>
              <div className="text-lg font-semibold text-slate-700">Dec 26, 2025</div>
            </div>
          </div>
        </div>

        {/* Timeline Overview */}
        <div className="bg-white rounded-xl shadow-lg p-8 mb-6">
          <h2 className="text-2xl font-bold text-slate-800 mb-6">📅 Timeline Overview</h2>
          <div className="relative">
            <div className="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
            {phases.map((phase, index) => {
              const Icon = phase.icon;
              const progress = getPhaseProgress(phase);
              return (
                <div key={phase.id} className="relative pl-12 pb-8 last:pb-0">
                  <div className={`absolute left-0 w-8 h-8 rounded-full ${statusColors[phase.status]} flex items-center justify-center`}>
                    <Icon className="w-5 h-5 text-white" />
                  </div>
                  <div
                    className={`bg-white border-2 rounded-lg p-4 cursor-pointer transition-all hover:shadow-md ${
                      selectedPhase === phase.id ? 'border-blue-500 shadow-md' : 'border-slate-200'
                    }`}
                    onClick={() => setSelectedPhase(selectedPhase === phase.id ? null : phase.id)}
                  >
                    <div className="flex items-center justify-between mb-2">
                      <h3 className="text-xl font-semibold text-slate-800">{phase.name}</h3>
                      <span className={`px-3 py-1 rounded-full text-xs font-medium ${priorityColors[phase.priority]}`}>
                        {phase.priority.toUpperCase()}
                      </span>
                    </div>
                    <div className="flex items-center gap-4 text-sm text-slate-600 mb-3">
                      <div className="flex items-center gap-1">
                        <Clock className="w-4 h-4" />
                        {phase.duration}
                      </div>
                      <div>Start: {phase.startDate}</div>
                      <div>{phase.tasks.length} tasks</div>
                    </div>
                    <div className="w-full bg-slate-200 rounded-full h-2">
                      <div
                        className={`h-2 rounded-full transition-all ${statusColors[phase.status]}`}
                        style={{ width: `${progress}%` }}
                      ></div>
                    </div>
                    <div className="text-right text-xs text-slate-600 mt-1">{progress}% complete</div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Phase Details */}
        {selectedPhase && (
          <div className="bg-white rounded-xl shadow-lg p-8">
            {phases.filter(p => p.id === selectedPhase).map(phase => {
              const Icon = phase.icon;
              return (
                <div key={phase.id}>
                  <div className="flex items-center gap-3 mb-6">
                    <Icon className={`w-8 h-8 ${iconColors[phase.color]}`} />
                    <h2 className="text-2xl font-bold text-slate-800">{phase.name}</h2>
                  </div>
                  <div className="space-y-4">
                    {phase.tasks.map(task => {
                      const isCompleted = completedTasks.has(task.id);
                      return (
                        <div
                          key={task.id}
                          className={`border-2 rounded-lg p-4 transition-all ${
                            isCompleted ? 'border-green-300 bg-green-50' : 'border-slate-200 hover:border-slate-300'
                          }`}
                        >
                          <div className="flex items-start gap-3">
                            <button
                              onClick={() => toggleTask(task.id)}
                              className="mt-1 flex-shrink-0"
                            >
                              {isCompleted ? (
                                <CheckCircle2 className="w-6 h-6 text-green-600" />
                              ) : (
                                <Circle className="w-6 h-6 text-slate-400" />
                              )}
                            </button>
                            <div className="flex-1">
                              <div className="flex items-start justify-between mb-2">
                                <h3 className={`text-lg font-semibold ${isCompleted ? 'text-green-800 line-through' : 'text-slate-800'}`}>
                                  {task.title}
                                </h3>
                                <span className={`px-2 py-1 rounded text-xs font-medium border ${priorityColors[task.priority]}`}>
                                  {task.priority}
                                </span>
                              </div>
                              <p className="text-slate-600 mb-3">{task.description}</p>
                              <div className="flex items-center gap-4 text-sm">
                                <div className="flex items-center gap-1 text-slate-500">
                                  <Clock className="w-4 h-4" />
                                  {task.estimation}
                                </div>
                                {task.dependencies.length > 0 && (
                                  <div className="text-slate-500">
                                    Depends on: {task.dependencies.join(', ')}
                                  </div>
                                )}
                              </div>
                            </div>
                          </div>
                        </div>
                      );
                    })}
                  </div>
                </div>
              );
            })}
          </div>
        )}

        {/* Quick Stats */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
          <div className="bg-white rounded-lg shadow p-4">
            <div className="text-sm text-slate-600 mb-1">Total Phases</div>
            <div className="text-3xl font-bold text-slate-800">{phases.length}</div>
          </div>
          <div className="bg-white rounded-lg shadow p-4">
            <div className="text-sm text-slate-600 mb-1">Total Tasks</div>
            <div className="text-3xl font-bold text-slate-800">
              {phases.reduce((sum, p) => sum + p.tasks.length, 0)}
            </div>
          </div>
          <div className="bg-white rounded-lg shadow p-4">
            <div className="text-sm text-slate-600 mb-1">Completed Tasks</div>
            <div className="text-3xl font-bold text-green-600">{completedTasks.size}</div>
          </div>
          <div className="bg-white rounded-lg shadow p-4">
            <div className="text-sm text-slate-600 mb-1">Overall Progress</div>
            <div className="text-3xl font-bold text-blue-600">
              {Math.round((completedTasks.size / phases.reduce((sum, p) => sum + p.tasks.length, 0)) * 100)}%
            </div>
          </div>
        </div>

        {/* Legend */}
        <div className="bg-white rounded-lg shadow p-6 mt-6">
          <h3 className="font-semibold text-slate-800 mb-3">Priority Levels</h3>
          <div className="flex flex-wrap gap-3">
            {Object.entries(priorityColors).map(([priority, className]) => (
              <span key={priority} className={`px-3 py-1 rounded-full text-sm font-medium border ${className}`}>
                {priority.charAt(0).toUpperCase() + priority.slice(1)}
              </span>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProjectRoadmap;
