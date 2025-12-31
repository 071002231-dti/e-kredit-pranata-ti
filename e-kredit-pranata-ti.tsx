import React, { useState, useEffect } from 'react';
import { Plus, Home, Calculator, History, TrendingUp, FileText, CheckCircle, AlertCircle, User, Upload, Calendar, Award } from 'lucide-react';

const EKreditPranataTI = () => {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [activities, setActivities] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState('');
  const [newActivity, setNewActivity] = useState({
    category: '',
    subcategory: '',
    title: '',
    date: '',
    volume: '',
    proof: null,
    status: 'pending'
  });

  // Data struktur kegiatan berdasarkan Pranata TI
  const creditStructure = {
    'pendidikan': {
      name: 'Pendidikan',
      icon: '🎓',
      subcategories: {
        's1': { name: 'S1 Teknik Informatika/sejenisnya', credit: 100 },
        's2': { name: 'S2 Teknik Informatika/sejenisnya', credit: 150 },
        's3': { name: 'S3 Teknik Informatika/sejenisnya', credit: 200 },
        'sertifikasi': { name: 'Sertifikasi Profesi TI', credit: 25 }
      }
    },
    'pelatihan': {
      name: 'Pelatihan',
      icon: '📚',
      subcategories: {
        'struktural': { name: 'Pelatihan Kepemimpinan', credit: 15 },
        'fungsional': { name: 'Pelatihan Fungsional', credit: 20 },
        'teknis': { name: 'Pelatihan Teknis TI', credit: 10 },
        'seminar': { name: 'Seminar/Workshop TI', credit: 5 }
      }
    },
    'tugas_pokok': {
      name: 'Pelaksanaan Tugas Pokok',
      icon: '💼',
      subcategories: {
        'analisis_sistem': { name: 'Melakukan Analisis Sistem', credit: 12.5 },
        'desain_sistem': { name: 'Merancang Sistem Informasi', credit: 15 },
        'implementasi': { name: 'Mengimplementasikan Sistem', credit: 20 },
        'maintenance': { name: 'Pemeliharaan Sistem', credit: 10 },
        'evaluasi': { name: 'Evaluasi Sistem Informasi', credit: 12.5 }
      }
    },
    'pengembangan_profesi': {
      name: 'Pengembangan Profesi',
      icon: '🚀',
      subcategories: {
        'penelitian': { name: 'Melakukan Penelitian TI', credit: 25 },
        'karya_tulis': { name: 'Membuat Karya Tulis TI', credit: 15 },
        'presentasi': { name: 'Presentasi Ilmiah', credit: 10 },
        'mentoring': { name: 'Membimbing Junior', credit: 5 }
      }
    },
    'penunjang': {
      name: 'Unsur Penunjang',
      icon: '🏆',
      subcategories: {
        'organisasi': { name: 'Keanggotaan Organisasi Profesi', credit: 5 },
        'penghargaan': { name: 'Memperoleh Penghargaan', credit: 10 },
        'publikasi': { name: 'Publikasi di Media', credit: 8 }
      }
    }
  };

  // Sample data for demonstration
  const sampleActivities = [
    {
      id: 1,
      category: 'pelatihan',
      subcategory: 'teknis',
      title: 'Pelatihan Cloud Computing AWS',
      date: '2024-03-15',
      volume: '40 jam',
      credit: 10,
      status: 'approved',
      proof: 'sertifikat_aws.pdf'
    },
    {
      id: 2,
      category: 'tugas_pokok',
      subcategory: 'implementasi',
      title: 'Implementasi Sistem Informasi Kepegawaian',
      date: '2024-02-20',
      volume: '1 sistem',
      credit: 20,
      status: 'pending',
      proof: 'laporan_implementasi.pdf'
    }
  ];

  useEffect(() => {
    setActivities(sampleActivities);
  }, []);

  const getTotalCredit = () => {
    return activities.reduce((total, activity) => total + (activity.credit || 0), 0);
  };

  const getApprovedCredit = () => {
    return activities
      .filter(activity => activity.status === 'approved')
      .reduce((total, activity) => total + (activity.credit || 0), 0);
  };

  const handleAddActivity = () => {
    if (newActivity.category && newActivity.subcategory && newActivity.title) {
      const creditValue = creditStructure[newActivity.category]?.subcategories[newActivity.subcategory]?.credit || 0;
      const activity = {
        id: Date.now(),
        ...newActivity,
        credit: creditValue,
        status: 'pending'
      };
      setActivities([...activities, activity]);
      setNewActivity({
        category: '',
        subcategory: '',
        title: '',
        date: '',
        volume: '',
        proof: null,
        status: 'pending'
      });
    }
  };

  const renderDashboard = () => (
    <div className="space-y-6">
      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-blue-100">Total Angka Kredit</p>
              <p className="text-3xl font-bold">{getTotalCredit()}</p>
            </div>
            <Calculator className="w-10 h-10 text-blue-200" />
          </div>
        </div>
        
        <div className="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-green-100">Kredit Disetujui</p>
              <p className="text-3xl font-bold">{getApprovedCredit()}</p>
            </div>
            <CheckCircle className="w-10 h-10 text-green-200" />
          </div>
        </div>
        
        <div className="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-xl shadow-lg">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-orange-100">Kegiatan Aktif</p>
              <p className="text-3xl font-bold">{activities.length}</p>
            </div>
            <TrendingUp className="w-10 h-10 text-orange-200" />
          </div>
        </div>
      </div>

      {/* Progress Simulation */}
      <div className="bg-white p-6 rounded-xl shadow-sm border">
        <h3 className="text-xl font-semibold mb-4 flex items-center">
          <Award className="w-6 h-6 mr-2 text-yellow-500" />
          Simulasi Kenaikan Jabatan
        </h3>
        <div className="space-y-4">
          <div>
            <div className="flex justify-between text-sm text-gray-600 mb-2">
              <span>Progress ke Pranata TI Muda (Target: 150 kredit)</span>
              <span>{Math.min(100, Math.round((getApprovedCredit() / 150) * 100))}%</span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-3">
              <div 
                className="bg-blue-500 h-3 rounded-full transition-all duration-500"
                style={{ width: `${Math.min(100, (getApprovedCredit() / 150) * 100)}%` }}
              ></div>
            </div>
          </div>
          <p className="text-sm text-gray-600">
            Anda memerlukan {Math.max(0, 150 - getApprovedCredit())} kredit lagi untuk mencapai target kenaikan.
          </p>
        </div>
      </div>

      {/* Recent Activities */}
      <div className="bg-white p-6 rounded-xl shadow-sm border">
        <h3 className="text-xl font-semibold mb-4">Kegiatan Terbaru</h3>
        <div className="space-y-3">
          {activities.slice(0, 5).map((activity) => (
            <div key={activity.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div className="flex items-center space-x-3">
                <div className="text-2xl">
                  {creditStructure[activity.category]?.icon}
                </div>
                <div>
                  <p className="font-medium">{activity.title}</p>
                  <p className="text-sm text-gray-500">{activity.date}</p>
                </div>
              </div>
              <div className="flex items-center space-x-2">
                <span className="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-medium">
                  {activity.credit} kredit
                </span>
                {activity.status === 'approved' ? (
                  <CheckCircle className="w-5 h-5 text-green-500" />
                ) : (
                  <AlertCircle className="w-5 h-5 text-orange-500" />
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );

  const renderInputActivity = () => (
    <div className="space-y-6">
      <div className="bg-white p-6 rounded-xl shadow-sm border">
        <h3 className="text-xl font-semibold mb-6 flex items-center">
          <Plus className="w-6 h-6 mr-2 text-blue-500" />
          Tambah Kegiatan Baru
        </h3>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Kategori Kegiatan
            </label>
            <select
              value={newActivity.category}
              onChange={(e) => {
                setNewActivity({...newActivity, category: e.target.value, subcategory: ''});
                setSelectedCategory(e.target.value);
              }}
              className="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Pilih Kategori</option>
              {Object.entries(creditStructure).map(([key, category]) => (
                <option key={key} value={key}>{category.name}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Sub Kategori
            </label>
            <select
              value={newActivity.subcategory}
              onChange={(e) => setNewActivity({...newActivity, subcategory: e.target.value})}
              className="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              disabled={!selectedCategory}
            >
              <option value="">Pilih Sub Kategori</option>
              {selectedCategory && Object.entries(creditStructure[selectedCategory]?.subcategories || {}).map(([key, sub]) => (
                <option key={key} value={key}>{sub.name} ({sub.credit} kredit)</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Judul Kegiatan
            </label>
            <input
              type="text"
              value={newActivity.title}
              onChange={(e) => setNewActivity({...newActivity, title: e.target.value})}
              className="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Masukkan judul kegiatan"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Tanggal Kegiatan
            </label>
            <input
              type="date"
              value={newActivity.date}
              onChange={(e) => setNewActivity({...newActivity, date: e.target.value})}
              className="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Volume/Durasi
            </label>
            <input
              type="text"
              value={newActivity.volume}
              onChange={(e) => setNewActivity({...newActivity, volume: e.target.value})}
              className="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="contoh: 40 jam, 1 sistem, dll"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Upload Bukti
            </label>
            <div className="w-full p-3 border-2 border-dashed border-gray-300 rounded-lg text-center hover:border-blue-500 transition-colors cursor-pointer">
              <Upload className="w-8 h-8 text-gray-400 mx-auto mb-2" />
              <p className="text-sm text-gray-600">Klik untuk upload file bukti</p>
              <p className="text-xs text-gray-400">PDF, JPG, PNG (Max 5MB)</p>
            </div>
          </div>
        </div>

        {selectedCategory && newActivity.subcategory && (
          <div className="mt-6 p-4 bg-blue-50 rounded-lg">
            <p className="text-sm text-blue-700">
              <strong>Nilai Kredit:</strong> {creditStructure[selectedCategory]?.subcategories[newActivity.subcategory]?.credit} kredit
            </p>
          </div>
        )}

        <div className="mt-6">
          <button
            onClick={handleAddActivity}
            className="bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center"
          >
            <Plus className="w-5 h-5 mr-2" />
            Tambah Kegiatan
          </button>
        </div>
      </div>
    </div>
  );

  const renderHistory = () => (
    <div className="space-y-6">
      <div className="bg-white p-6 rounded-xl shadow-sm border">
        <h3 className="text-xl font-semibold mb-6 flex items-center">
          <History className="w-6 h-6 mr-2 text-green-500" />
          Riwayat Kegiatan
        </h3>
        
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b-2 border-gray-200">
                <th className="text-left py-3 px-4">Tanggal</th>
                <th className="text-left py-3 px-4">Kegiatan</th>
                <th className="text-left py-3 px-4">Kategori</th>
                <th className="text-left py-3 px-4">Volume</th>
                <th className="text-left py-3 px-4">Kredit</th>
                <th className="text-left py-3 px-4">Status</th>
                <th className="text-left py-3 px-4">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {activities.map((activity) => (
                <tr key={activity.id} className="border-b border-gray-100 hover:bg-gray-50">
                  <td className="py-3 px-4">{activity.date}</td>
                  <td className="py-3 px-4 font-medium">{activity.title}</td>
                  <td className="py-3 px-4">
                    <span className="inline-flex items-center">
                      <span className="mr-1">{creditStructure[activity.category]?.icon}</span>
                      {creditStructure[activity.category]?.name}
                    </span>
                  </td>
                  <td className="py-3 px-4">{activity.volume}</td>
                  <td className="py-3 px-4">
                    <span className="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-medium">
                      {activity.credit}
                    </span>
                  </td>
                  <td className="py-3 px-4">
                    {activity.status === 'approved' ? (
                      <span className="inline-flex items-center text-green-600">
                        <CheckCircle className="w-4 h-4 mr-1" />
                        Disetujui
                      </span>
                    ) : (
                      <span className="inline-flex items-center text-orange-600">
                        <AlertCircle className="w-4 h-4 mr-1" />
                        Pending
                      </span>
                    )}
                  </td>
                  <td className="py-3 px-4">
                    <button className="text-blue-500 hover:text-blue-700 text-sm">
                      <FileText className="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );

  const renderCalculator = () => (
    <div className="space-y-6">
      <div className="bg-white p-6 rounded-xl shadow-sm border">
        <h3 className="text-xl font-semibold mb-6 flex items-center">
          <Calculator className="w-6 h-6 mr-2 text-purple-500" />
          Kalkulator Angka Kredit
        </h3>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <h4 className="font-semibold mb-4">Ringkasan per Kategori</h4>
            <div className="space-y-3">
              {Object.entries(creditStructure).map(([key, category]) => {
                const categoryTotal = activities
                  .filter(activity => activity.category === key)
                  .reduce((total, activity) => total + activity.credit, 0);
                
                return (
                  <div key={key} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span className="flex items-center">
                      <span className="text-xl mr-2">{category.icon}</span>
                      {category.name}
                    </span>
                    <span className="font-bold text-blue-600">{categoryTotal} kredit</span>
                  </div>
                );
              })}
            </div>
          </div>
          
          <div>
            <h4 className="font-semibold mb-4">Target Kenaikan Jabatan</h4>
            <div className="space-y-4">
              <div className="p-4 border-l-4 border-blue-500 bg-blue-50">
                <h5 className="font-medium">Pranata TI Pertama → Pranata TI Muda</h5>
                <p className="text-sm text-gray-600">Target: 150 kredit</p>
                <p className="text-sm text-blue-600">Sisa: {Math.max(0, 150 - getTotalCredit())} kredit</p>
              </div>
              
              <div className="p-4 border-l-4 border-green-500 bg-green-50">
                <h5 className="font-medium">Pranata TI Muda → Pranata TI Madya</h5>
                <p className="text-sm text-gray-600">Target: 300 kredit</p>
                <p className="text-sm text-green-600">Sisa: {Math.max(0, 300 - getTotalCredit())} kredit</p>
              </div>
              
              <div className="p-4 border-l-4 border-purple-500 bg-purple-50">
                <h5 className="font-medium">Pranata TI Madya → Pranata TI Utama</h5>
                <p className="text-sm text-gray-600">Target: 550 kredit</p>
                <p className="text-sm text-purple-600">Sisa: {Math.max(0, 550 - getTotalCredit())} kredit</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );

  const navigation = [
    { id: 'dashboard', name: 'Dashboard', icon: Home },
    { id: 'input', name: 'Input Kegiatan', icon: Plus },
    { id: 'history', name: 'Riwayat', icon: History },
    { id: 'calculator', name: 'Kalkulator', icon: Calculator },
  ];

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-4">
            <div className="flex items-center space-x-3">
              <div className="bg-blue-500 text-white p-2 rounded-lg">
                <User className="w-6 h-6" />
              </div>
              <div>
                <h1 className="text-2xl font-bold text-gray-900">e-Kredit Pranata TI</h1>
                <p className="text-sm text-gray-600">Sistem Pengelolaan Angka Kredit</p>
              </div>
            </div>
            <div className="flex items-center space-x-4">
              <div className="text-right">
                <p className="font-medium">Ahmad Pratama, S.Kom</p>
                <p className="text-sm text-gray-600">NIP: 198901012020121001</p>
              </div>
            </div>
          </div>
        </div>
      </header>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="flex flex-col lg:flex-row gap-8">
          {/* Sidebar Navigation */}
          <div className="lg:w-64 flex-shrink-0">
            <nav className="bg-white rounded-xl shadow-sm border p-4">
              <div className="space-y-2">
                {navigation.map((item) => {
                  const Icon = item.icon;
                  return (
                    <button
                      key={item.id}
                      onClick={() => setActiveTab(item.id)}
                      className={`w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-left transition-colors ${
                        activeTab === item.id
                          ? 'bg-blue-500 text-white'
                          : 'text-gray-600 hover:bg-gray-100'
                      }`}
                    >
                      <Icon className="w-5 h-5" />
                      <span className="font-medium">{item.name}</span>
                    </button>
                  );
                })}
              </div>
            </nav>
          </div>

          {/* Main Content */}
          <div className="flex-1">
            {activeTab === 'dashboard' && renderDashboard()}
            {activeTab === 'input' && renderInputActivity()}
            {activeTab === 'history' && renderHistory()}
            {activeTab === 'calculator' && renderCalculator()}
          </div>
        </div>
      </div>
    </div>
  );
};

export default EKreditPranataTI;