import React, { useEffect, useState } from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { RootState } from '../stores/store'
import {
  fetchGuidesStart,
  fetchGuidesSuccess,
  fetchGuidesFailure,
  addGuide,
  updateGuide,
  deleteGuide,
} from '../stores/slices/guideSlice'
import axios from 'axios'
import { toast } from 'react-toastify'

const Guides: React.FC = () => {
  const dispatch = useDispatch()
  const { guides, loading, error } = useSelector((state: RootState) => state.guides)
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [selectedGuide, setSelectedGuide] = useState<any>(null)
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    photo: '',
    status: 'ACTIVE',
    country: '',
  })

  useEffect(() => {
    fetchGuides()
  }, [])

  const fetchGuides = async () => {
    try {
      dispatch(fetchGuidesStart())
      const response = await axios.get('/api/guides')
      dispatch(fetchGuidesSuccess(response.data))
    } catch (error) {
      dispatch(fetchGuidesFailure('Erreur lors du chargement des guides'))
      toast.error('Erreur lors du chargement des guides')
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    try {
      if (selectedGuide) {
        const response = await axios.put(`/api/guides/${selectedGuide.id}`, formData)
        dispatch(updateGuide(response.data))
        toast.success('Guide mis à jour avec succès')
      } else {
        const response = await axios.post('/api/guides', formData)
        dispatch(addGuide(response.data))
        toast.success('Guide ajouté avec succès')
      }
      setIsModalOpen(false)
      resetForm()
    } catch (error) {
      toast.error('Erreur lors de l\'enregistrement du guide')
    }
  }

  const handleDelete = async (id: number) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer ce guide ?')) {
      try {
        await axios.delete(`/api/guides/${id}`)
        dispatch(deleteGuide(id))
        toast.success('Guide supprimé avec succès')
      } catch (error) {
        toast.error('Erreur lors de la suppression du guide')
      }
    }
  }

  const resetForm = () => {
    setFormData({
      firstName: '',
      lastName: '',
      photo: '',
      status: 'ACTIVE',
      country: '',
    })
    setSelectedGuide(null)
  }

  return (
    <div className="container mx-auto px-4">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Gestion des Guides</h1>
        <button
          onClick={() => {
            resetForm()
            setIsModalOpen(true)
          }}
          className="btn btn-primary"
        >
          Ajouter un guide
        </button>
      </div>

      {loading ? (
        <div className="text-center">Chargement...</div>
      ) : error ? (
        <div className="text-red-500">{error}</div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {guides.map((guide) => (
            <div key={guide.id} className="card">
              <div className="flex items-center space-x-4">
                <img
                  src={guide.photo}
                  alt={`${guide.firstName} ${guide.lastName}`}
                  className="w-16 h-16 rounded-full object-cover"
                />
                <div>
                  <h3 className="font-semibold">
                    {guide.firstName} {guide.lastName}
                  </h3>
                  <p className="text-gray-600">{guide.country}</p>
                  <span
                    className={`inline-block px-2 py-1 rounded text-sm ${
                      guide.status === 'ACTIVE'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    }`}
                  >
                    {guide.status === 'ACTIVE' ? 'Actif' : 'Inactif'}
                  </span>
                </div>
              </div>
              <div className="mt-4 flex justify-end space-x-2">
                <button
                  onClick={() => {
                    setSelectedGuide(guide)
                    setFormData({
                      firstName: guide.firstName,
                      lastName: guide.lastName,
                      photo: guide.photo,
                      status: guide.status,
                      country: guide.country,
                    })
                    setIsModalOpen(true)
                  }}
                  className="btn btn-secondary"
                >
                  Modifier
                </button>
                <button
                  onClick={() => handleDelete(guide.id)}
                  className="btn bg-red-600 text-white hover:bg-red-700"
                >
                  Supprimer
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {isModalOpen && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
          <div className="bg-white p-6 rounded-lg w-full max-w-md">
            <h2 className="text-xl font-bold mb-4">
              {selectedGuide ? 'Modifier le guide' : 'Ajouter un guide'}
            </h2>
            <form onSubmit={handleSubmit}>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Prénom
                  </label>
                  <input
                    type="text"
                    value={formData.firstName}
                    onChange={(e) =>
                      setFormData({ ...formData, firstName: e.target.value })
                    }
                    className="input"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Nom
                  </label>
                  <input
                    type="text"
                    value={formData.lastName}
                    onChange={(e) =>
                      setFormData({ ...formData, lastName: e.target.value })
                    }
                    className="input"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Photo URL
                  </label>
                  <input
                    type="text"
                    value={formData.photo}
                    onChange={(e) =>
                      setFormData({ ...formData, photo: e.target.value })
                    }
                    className="input"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Pays
                  </label>
                  <input
                    type="text"
                    value={formData.country}
                    onChange={(e) =>
                      setFormData({ ...formData, country: e.target.value })
                    }
                    className="input"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Statut
                  </label>
                  <select
                    value={formData.status}
                    onChange={(e) =>
                      setFormData({ ...formData, status: e.target.value })
                    }
                    className="input"
                  >
                    <option value="ACTIVE">Actif</option>
                    <option value="INACTIVE">Inactif</option>
                  </select>
                </div>
              </div>
              <div className="mt-6 flex justify-end space-x-2">
                <button
                  type="button"
                  onClick={() => {
                    setIsModalOpen(false)
                    resetForm()
                  }}
                  className="btn btn-secondary"
                >
                  Annuler
                </button>
                <button type="submit" className="btn btn-primary">
                  {selectedGuide ? 'Mettre à jour' : 'Ajouter'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  )
}

export default Guides 