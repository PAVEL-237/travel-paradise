import React from 'react'
import { Link } from 'react-router-dom'
import { useAuth } from '../../hooks/useAuth'
import { FaUser, FaSignOutAlt, FaSignInAlt, FaUserPlus } from 'react-icons/fa'
import { MdTravelExplore } from 'react-icons/md'

const Navbar: React.FC = () => {
  const { user, logout } = useAuth()

  return (
    <nav className="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          <div className="flex items-center">
            <Link to="/" className="flex items-center space-x-2">
              <MdTravelExplore className="h-8 w-8 text-white" />
              <span className="text-white text-xl font-bold">Travel Paradise</span>
            </Link>
          </div>

          <div className="flex items-center space-x-4">
            {user ? (
              <>
                <Link
                  to="/dashboard"
                  className="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium flex items-center space-x-1"
                >
                  <FaUser className="h-4 w-4" />
                  <span>{user.email}</span>
                </Link>
                <button
                  onClick={logout}
                  className="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-1 transition duration-150 ease-in-out"
                >
                  <FaSignOutAlt className="h-4 w-4" />
                  <span>Déconnexion</span>
                </button>
              </>
            ) : (
              <>
                <Link
                  to="/login"
                  className="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium flex items-center space-x-1"
                >
                  <FaSignInAlt className="h-4 w-4" />
                  <span>Connexion</span>
                </Link>
                <Link
                  to="/register"
                  className="bg-white text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-1 transition duration-150 ease-in-out"
                >
                  <FaUserPlus className="h-4 w-4" />
                  <span>Inscription</span>
                </Link>
              </>
            )}
          </div>
        </div>
      </div>
    </nav>
  )
}

export default Navbar 