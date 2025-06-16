import React from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { Link, useNavigate } from 'react-router-dom'
import { logout } from '../../stores/slices/authSlice'
import { RootState } from '../../stores/store'
import { Button } from '@mui/material'
import { AccountCircle, ExitToApp } from '@mui/icons-material'

const Navbar: React.FC = () => {
  const dispatch = useDispatch()
  const navigate = useNavigate()
  const { user } = useSelector((state: RootState) => state.auth)

  const handleLogout = () => {
    dispatch(logout())
    navigate('/')
  }

  return (
    <nav className="bg-white shadow-md fixed w-full top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16">
          <div className="flex">
            <div className="flex-shrink-0 flex items-center">
              <Link to="/" className="text-2xl font-bold text-primary-600">
                Travel Paradise
              </Link>
            </div>
            <div className="hidden sm:ml-6 sm:flex sm:space-x-8">
              <Link to="/" className="text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-primary-500">
                Accueil
              </Link>
              <Link to="/destinations" className="text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-primary-500">
                Destinations
              </Link>
              <Link to="/about" className="text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-primary-500">
                À propos
              </Link>
            </div>
          </div>

          <div className="flex items-center">
            {user ? (
              <div className="flex items-center space-x-4">
                <span className="text-gray-700 flex items-center">
                  <AccountCircle className="mr-2" />
                  {user.firstName} {user.lastName}
                </span>
                <Button
                  variant="outlined"
                  color="primary"
                  startIcon={<ExitToApp />}
                  onClick={handleLogout}
                  className="ml-4"
                >
                  Déconnexion
                </Button>
              </div>
            ) : (
              <Button
                variant="contained"
                color="primary"
                component={Link}
                to="/login"
                className="ml-4"
              >
                Connexion
              </Button>
            )}
          </div>
        </div>
      </div>
    </nav>
  )
}

export default Navbar 