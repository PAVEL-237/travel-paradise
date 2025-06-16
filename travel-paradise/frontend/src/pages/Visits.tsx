import React, { useEffect, useState } from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { RootState } from '../stores/store'
import {
  fetchVisitsStart,
  fetchVisitsSuccess,
  fetchVisitsFailure,
  addVisit,
  updateVisit,
  deleteVisit,
  updateVisitStatus,
  updateVisitComment,
} from '../stores/slices/visitSlice'
import axios from 'axios'
import { toast } from 'react-toastify'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'

const Visits: React.FC = () => {
  const dispatch = useDispatch()
  const { visits, loading, error } = useSelector((state: RootState) => state.visits)
  const { guides } = useSelector((state: RootState) => state.guides)
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [selectedVisit, setSelectedVisit] = useState<any>(null)
  const [formData, setFormData] = useState({
    photo: '',
    country: '',
    location: '',
    date: '',
    startTime: '',
    duration: 60,
    guideId: '',
    status: 'SCHEDULED',
    generalComment: '',
  })

  useEffect(() => {
    fetchVisits()
  }, [])

  const fetchVisits = async () => {
    try {
      dispatch(fetchVisitsStart())
      const response = await axios.get('/api/visits')
      dispatch(fetchVisitsSuccess(response.data))
    } catch (error) {
      dispatch(fetchVisitsFailure('Erreur lors du chargement des visites'))
      toast.error('Erreur lors du chargement des visites')
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    try {
      if (selectedVisit) {
        const response = await axios.put(`/api/visits/${selectedVisit.id}`, formData)
        dispatch(updateVisit(response.data))
        toast.success('Visite mise à jour avec succès')
      } else {
        const response = await axios.post('/api/visits', formData)
        dispatch(addVisit(response.data))
        toast.success('Visite ajoutée avec succès')
      }
      setIsModalOpen(false)
      resetForm()
    } catch (error) {
      toast.error('Erreur lors de l\'enregistrement de la visite')
    }
  }

  const handleDelete = async (id: number) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer cette visite ?')) {
      try {
        await axios.delete(`/api/visits/${id}`)
        dispatch(deleteVisit(id))
        toast.success('Visite supprimée avec succès')
      } catch (error) {
        toast.error('Erreur lors de la suppression de la visite')
      }
    }
  }

  const handleStatusChange = async (id: number, status: string) => {
    try {
      await axios.patch(`/api/visits/${id}/status`, { status })
      dispatch(updateVisitStatus({ id, status }))
      toast.success('Statut mis à jour avec succès')
    } catch (error) {
      toast.error('Erreur lors de la mise à jour du statut')
    }
  }

  const resetForm = () => {
    setFormData({
      photo: '',
      country: '',
      location: '',
      date: '',
      startTime: '',
      duration: 60,
      guideId: '',
      status: 'SCHEDULED',
      generalComment: '',
    })
    setSelectedVisit(null)
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'SCHEDULED':
        return 'bg-blue-100 text-blue-800'
      case 'IN_PROGRESS':
        return 'bg-yellow-100 text-yellow-800'
      case 'COMPLETED':
        return 'bg-green-100 text-green-800'
      case 'CANCELLED':
        return 'bg-red-100 text-red-800'
      default:
        return 'bg-gray-100 text-gray-800'
    }
  }

  const getStatusLabel = (status: string) => {
    switch (status) {
      case 'SCHEDULED':
        return 'Planifiée'
      case 'IN_PROGRESS':
        return 'En cours'
      case 'COMPLETED':
        return 'Terminée'
      case 'CANCELLED':
        return 'Annulée'
      default:
        return status
    }
  }

  return (
    <div className="container mx-auto px-4">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Gestion des Visites</h1>
        <button
          onClick={() => {
            resetForm()
            setIsModalOpen(true)
          }}
          className="btn btn-primary"
        >
          Ajouter une visite
        </button>
      </div>

      {loading ? (
        <div className="text-center">Chargement...</div>
      ) : error ? (
        <div className="text-red-500">{error}</div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {visits.map((visit) => (
            <div key={visit.id} className="card">
              <img
                src={visit.photo}
                alt={visit.location}
                className="w-full h-48 object-cover rounded-t-lg"
              />
              <div className="p-4">
                <h3 className="font-semibold text-lg mb-2">{visit.location}</h3>
                <p className="text-gray-600 mb-2">{visit.country}</p>
                <p className="text-gray-600 mb-2">
                  {format(new Date(visit.date), 'PPP', { locale: fr })}
                </p>
                <p className="text-gray-600 mb-2">
                  {visit.startTime} - {visit.endTime}
                </p>
                <p className="text-gray-600 mb-2">
                  Guide: {guides.find(g => g.id === visit.guideId)?.firstName} {guides.find(g => g.id === visit.guideId)?.lastName}
                </p>
                <span
                  className={`inline-block px-2 py-1 rounded text-sm ${getStatusColor(
                    visit.status
                  )}`}
                >
                  {getStatusLabel(visit.status)}
                </span>
                {visit.generalComment && (
                  <p className="mt-2 text-gray-600">{visit.generalComment}</p>
                )}
              </div>
              <div className="p-4 border-t flex justify-end space-x-2">
                <button
                  onClick={() => {
                    setSelectedVisit(visit)
                    setFormData({
                      photo: visit.photo,
                      country: visit.country,
                      location: visit.location,
                      date: visit.date,
                      startTime: visit.startTime,
                      duration: visit.duration,
                      guideId: visit.guideId.toString(),
                      status: visit.status,
                      generalComment: visit.generalComment || '',
                    })
                    setIsModalOpen(true)
                  }}
                  className="btn btn-secondary"
                >
                  Modifier
                </button>
                <button
                  onClick={() => handleDelete(visit.id)}
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
              {selectedVisit ? 'Modifier la visite' : 'Ajouter une visite'}
            </h2>
            <form onSubmit={handleSubmit}>
              <div className="space-y-4">
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
                    Lieu
                  </label>
                  <input
                    type="text"
                    value={formData.location}
                    onChange={(e) =>
                      setFormData({ ...formData, location: e.target.value })
                    }
                    className="input"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Date
                  </label>
                  <input
                    type="date"
                    value={formData.date}
                    onChange={(e) =>
                      setFormData({ ...formData, date: e.target.value })
                    }
                    className="input"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Heure de début
                  </label>
                  <input
                    type="time"
                    value={formData.startTime}
                    onChange={(e) =>
                      setFormData({ ...formData, startTime: e.target.value })
                    }
                    className="input"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Durée (minutes)
                  </label>
                  <input
                    type="number"
                    value={formData.duration}
                    onChange={(e) =>
                      setFormData({ ...formData, duration: parseInt(e.target.value) })
                    }
                    className="input"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Guide
                  </label>
                  <select
                    value={formData.guideId}
                    onChange={(e) =>
                      setFormData({ ...formData, guideId: e.target.value })
                    }
                    className="input"
                    required
                  >
                    <option value="">Sélectionner un guide</option>
                    {guides.map((guide) => (
                      <option key={guide.id} value={guide.id}>
                        {guide.firstName} {guide.lastName}
                      </option>
                    ))}
                  </select>
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
                    <option value="SCHEDULED">Planifiée</option>
                    <option value="IN_PROGRESS">En cours</option>
                    <option value="COMPLETED">Terminée</option>
                    <option value="CANCELLED">Annulée</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Commentaire général
                  </label>
                  <textarea
                    value={formData.generalComment}
                    onChange={(e) =>
                      setFormData({ ...formData, generalComment: e.target.value })
                    }
                    className="input"
                    rows={3}
                  />
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
                  {selectedVisit ? 'Mettre à jour' : 'Ajouter'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  )
}

export default Visits 