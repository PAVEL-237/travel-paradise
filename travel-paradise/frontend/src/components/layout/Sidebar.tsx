import React from 'react'
import { NavLink } from 'react-router-dom'
import { useSelector } from 'react-redux'
import { RootState } from '../../stores/store'
import { FaUsers, FaMapMarkedAlt, FaChartBar, FaUserCog } from 'react-icons/fa'

const Sidebar: React.FC = () => {
  const { user } = useSelector((state: RootState) => state.auth)

  const adminLinks = [
    { to: '/guides', label: 'Guides', icon: <FaUsers /> },
    { to: '/visits', label: 'Visites', icon: <FaMapMarkedAlt /> },
    { to: '/statistics', label: 'Statistiques', icon: <FaChartBar /> },
    { to: '/users', label: 'Utilisateurs', icon: <FaUserCog /> },
  ]

  const guideLinks = [
    { to: '/my-visits', label: 'Mes Visites', icon: <FaMapMarkedAlt /> },
  ]

  const links = user?.role === 'ADMIN' ? adminLinks : guideLinks

  return (
    <aside className="w-64 bg-white shadow-md h-screen">
      <div className="p-4">
        <nav className="space-y-2">
          {links.map((link) => (
            <NavLink
              key={link.to}
              to={link.to}
              className={({ isActive }) =>
                `flex items-center space-x-2 p-2 rounded-md transition-colors ${
                  isActive
                    ? 'bg-primary-100 text-primary-600'
                    : 'text-gray-600 hover:bg-gray-100'
                }`
              }
            >
              <span className="text-xl">{link.icon}</span>
              <span>{link.label}</span>
            </NavLink>
          ))}
        </nav>
      </div>
    </aside>
  )
}

export default Sidebar 