import React, { useEffect, useState } from 'react'
import { useSelector } from 'react-redux'
import { RootState } from '../stores/store'
import axios from 'axios'
import { toast } from 'react-toastify'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'

interface Visit {
  id: number
  photo: string
  country: string
  location: string
  date: string
  startTime: string
  duration: number
  endTime: string
  status: 'SCHEDULED' | 'IN_PROGRESS' | 'COMPLETED' | 'CANCELLED'
  generalComment?: string
  visitors: {
    id: number
    firstName: string
    lastName: string
    isPresent: boolean
    comments?: string
  }[]
}

const MyVisits: React.FC = () => {
  const { user } = useSelector((state: RootState) => state.auth)
  const [visits, setVisits] = useState<Visit[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [selectedVisit, setSelectedVisit] = useState<Visit | null>(null)
  const [isModalOpen, setIsModalOpen] = useState(false)

  useEffect(() => {
    fetchVisits()
  }, [])

  const fetchVisits = async () => {
    try {
      setLoading(true)
      const response = await axios.get('/api/guides/my-visits')
      setVisits(response.data)
      setError(null)
    } catch (error) {
      setError('Erreur lors du chargement des visites')
      toast.error('Erreur lors du chargement des visites')
    } finally {
      setLoading(false)
    }
  }

  const handleStatusChange = async (visitId: number, status: Visit['status']) => {
    try {
      await axios.patch(`/api/visits/${visitId}/status`, { status })
      setVisits(visits.map(visit =>
        visit.id === visitId ? { ...visit, status } : visit
      ))
      toast.success('Statut mis à jour avec succès')
    } catch (error) {
      toast.error('Erreur lors de la mise à jour du statut')
    }
  }

  const handleVisitorPresence = async (visitId: number, visitorId: number, isPresent: boolean) => {
    try {
      await axios.patch(`/api/visits/${visitId}/visitors/${visitorId}/presence`, { isPresent })
      setVisits(visits.map(visit =>
        visit.id === visitId
          ? {
              ...visit,
              visitors: visit.visitors.map(visitor =>
                visitor.id === visitorId ? { ...visitor, isPresent } : visitor
              ),
            }
          : visit
      ))
      toast.success('Présence mise à jour avec succès')
    } catch (error) {
      toast.error('Erreur lors de la mise à jour de la présence')
    }
  }

  const handleVisitorComment = async (visitId: number, visitorId: number, comments: string) => {
    try {
      await axios.patch(`/api/visits/${visitId}/visitors/${visitorId}/comments`, { comments })
      setVisits(visits.map(visit =>
        visit.id === visitId
          ? {
              ...visit,
              visitors: visit.visitors.map(visitor =>
                visitor.id === visitorId ? { ...visitor, comments } : visitor
              ),
            }
          : visit
      ))
      toast.success('Commentaire ajouté avec succès')
    } catch (error) {
      toast.error('Erreur lors de l\'ajout du commentaire')
    }
  }

  const handleGeneralComment = async (visitId: number, comment: string) => {
    try {
      await axios.patch(`/api/visits/${visitId}/comment`, { comment })
      setVisits(visits.map(visit =>
        visit.id === visitId ? { ...visit, generalComment: comment } : visit
      ))
      toast.success('Commentaire général ajouté avec succès')
      setIsModalOpen(false)
    } catch (error) {
      toast.error('Erreur lors de l\'ajout du commentaire général')
    }
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

  if (loading) {
    return <div className="text-center">Chargement...</div>
  }

  if (error) {
    return <div className="text-red-500">{error}</div>
  }

  return (
    <div className="container mx-auto px-4">
      <h1 className="text-2xl font-bold mb-6">Mes Visites</h1>

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
              <span
                className={`inline-block px-2 py-1 rounded text-sm ${getStatusColor(
                  visit.status
                )}`}
              >
                {getStatusLabel(visit.status)}
              </span>

              {visit.status !== 'CANCELLED' && (
                <div className="mt-4">
                  <h4 className="font-semibold mb-2">Liste des visiteurs</h4>
                  <div className="space-y-2">
                    {visit.visitors.map((visitor) => (
                      <div key={visitor.id} className="flex items-center justify-between">
                        <span>
                          {visitor.firstName} {visitor.lastName}
                        </span>
                        <div className="flex items-center space-x-2">
                          <label className="flex items-center">
                            <input
                              type="checkbox"
                              checked={visitor.isPresent}
                              onChange={(e) =>
                                handleVisitorPresence(visit.id, visitor.id, e.target.checked)
                              }
                              className="form-checkbox h-4 w-4 text-primary-600"
                              disabled={visit.status === 'COMPLETED'}
                            />
                            <span className="ml-2 text-sm">Présent</span>
                          </label>
                          <button
                            onClick={() => {
                              const comment = prompt('Ajouter un commentaire:', visitor.comments)
                              if (comment !== null) {
                                handleVisitorComment(visit.id, visitor.id, comment)
                              }
                            }}
                            className="text-sm text-primary-600 hover:text-primary-700"
                            disabled={visit.status === 'COMPLETED'}
                          >
                            Commentaire
                          </button>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              <div className="mt-4 flex justify-between items-center">
                {visit.status !== 'COMPLETED' && visit.status !== 'CANCELLED' && (
                  <select
                    value={visit.status}
                    onChange={(e) =>
                      handleStatusChange(visit.id, e.target.value as Visit['status'])
                    }
                    className="input"
                  >
                    <option value="SCHEDULED">Planifiée</option>
                    <option value="IN_PROGRESS">En cours</option>
                    <option value="COMPLETED">Terminée</option>
                    <option value="CANCELLED">Annulée</option>
                  </select>
                )}
                <button
                  onClick={() => {
                    setSelectedVisit(visit)
                    setIsModalOpen(true)
                  }}
                  className="btn btn-secondary"
                >
                  Commentaire général
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>

      {isModalOpen && selectedVisit && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
          <div className="bg-white p-6 rounded-lg w-full max-w-md">
            <h2 className="text-xl font-bold mb-4">Commentaire général</h2>
            <textarea
              value={selectedVisit.generalComment || ''}
              onChange={(e) =>
                setSelectedVisit({ ...selectedVisit, generalComment: e.target.value })
              }
              className="input mb-4"
              rows={4}
            />
            <div className="flex justify-end space-x-2">
              <button
                onClick={() => setIsModalOpen(false)}
                className="btn btn-secondary"
              >
                Annuler
              </button>
              <button
                onClick={() => handleGeneralComment(selectedVisit.id, selectedVisit.generalComment || '')}
                className="btn btn-primary"
              >
                Enregistrer
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

export default MyVisits 