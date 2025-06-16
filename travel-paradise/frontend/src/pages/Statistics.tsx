import React, { useEffect, useState } from 'react'
import axios from 'axios'
import { toast } from 'react-toastify'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
} from 'chart.js'
import { Bar, Pie } from 'react-chartjs-2'
import { API_ENDPOINTS } from '../config'

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement
)

interface Statistics {
  visitsPerMonth: {
    month: string
    count: number
  }[]
  visitsPerGuide: {
    guideName: string
    count: number
  }[]
  attendanceRate: {
    month: string
    rate: number
  }[]
}

const Statistics: React.FC = () => {
  const [statistics, setStatistics] = useState<Statistics | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    fetchStatistics()
  }, [])

  const fetchStatistics = async () => {
    try {
      setLoading(true)
      const [visitsPerMonth, visitsPerGuide, attendanceRate] = await Promise.all([
        axios.get(API_ENDPOINTS.STATISTICS.VISITS_BY_MONTH),
        axios.get(API_ENDPOINTS.STATISTICS.VISITS_BY_GUIDE),
        axios.get(API_ENDPOINTS.STATISTICS.ATTENDANCE_RATE)
      ])

      setStatistics({
        visitsPerMonth: visitsPerMonth.data,
        visitsPerGuide: visitsPerGuide.data,
        attendanceRate: attendanceRate.data
      })
      setError(null)
    } catch (error) {
      console.error('Error fetching statistics:', error)
      setError('Erreur lors du chargement des statistiques')
      toast.error('Erreur lors du chargement des statistiques')
    } finally {
      setLoading(false)
    }
  }

  const visitsPerMonthData = {
    labels: statistics?.visitsPerMonth.map(item => item.month) || [],
    datasets: [
      {
        label: 'Nombre de visites',
        data: statistics?.visitsPerMonth.map(item => item.count) || [],
        backgroundColor: 'rgba(53, 162, 235, 0.5)',
      },
    ],
  }

  const visitsPerGuideData = {
    labels: statistics?.visitsPerGuide.map(item => item.guideName) || [],
    datasets: [
      {
        label: 'Nombre de visites',
        data: statistics?.visitsPerGuide.map(item => item.count) || [],
        backgroundColor: [
          'rgba(255, 99, 132, 0.5)',
          'rgba(54, 162, 235, 0.5)',
          'rgba(255, 206, 86, 0.5)',
          'rgba(75, 192, 192, 0.5)',
          'rgba(153, 102, 255, 0.5)',
        ],
      },
    ],
  }

  const attendanceRateData = {
    labels: statistics?.attendanceRate.map(item => item.month) || [],
    datasets: [
      {
        label: 'Taux de présence (%)',
        data: statistics?.attendanceRate.map(item => item.rate) || [],
        backgroundColor: 'rgba(75, 192, 192, 0.5)',
      },
    ],
  }

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
          <p className="mt-4 text-gray-600">Chargement des statistiques...</p>
        </div>
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-center">
          <div className="text-red-500 text-xl mb-4">⚠️</div>
          <p className="text-red-500">{error}</p>
          <button
            onClick={fetchStatistics}
            className="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Réessayer
          </button>
        </div>
      </div>
    )
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-8">Statistiques</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div className="bg-white rounded-lg shadow-lg p-6">
          <h2 className="text-xl font-semibold mb-4">Visites par mois</h2>
          <Bar
            data={visitsPerMonthData}
            options={{
              responsive: true,
              plugins: {
                legend: {
                  position: 'top' as const,
                },
                title: {
                  display: true,
                  text: 'Nombre de visites par mois',
                },
              },
            }}
          />
        </div>

        <div className="bg-white rounded-lg shadow-lg p-6">
          <h2 className="text-xl font-semibold mb-4">Visites par guide</h2>
          <Pie
            data={visitsPerGuideData}
            options={{
              responsive: true,
              plugins: {
                legend: {
                  position: 'top' as const,
                },
                title: {
                  display: true,
                  text: 'Répartition des visites par guide',
                },
              },
            }}
          />
        </div>

        <div className="bg-white rounded-lg shadow-lg p-6 md:col-span-2">
          <h2 className="text-xl font-semibold mb-4">Taux de présence</h2>
          <Bar
            data={attendanceRateData}
            options={{
              responsive: true,
              plugins: {
                legend: {
                  position: 'top' as const,
                },
                title: {
                  display: true,
                  text: 'Taux de présence des touristes par mois',
                },
              },
              scales: {
                y: {
                  beginAtZero: true,
                  max: 100,
                  title: {
                    display: true,
                    text: 'Pourcentage (%)',
                  },
                },
              },
            }}
          />
        </div>
      </div>
    </div>
  )
}

export default Statistics 