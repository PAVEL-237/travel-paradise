import React from 'react';
import { Link } from 'react-router-dom';
import LoginForm from '../../components/auth/LoginForm';
import { MdTravelExplore } from 'react-icons/md';

const LoginPage: React.FC = () => {
  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl">
        <div className="text-center">
          <div className="flex justify-center">
            <MdTravelExplore className="h-12 w-12 text-blue-600" />
          </div>
          <h2 className="mt-6 text-3xl font-extrabold text-gray-900">
            Bienvenue sur Travel Paradise
          </h2>
          <p className="mt-2 text-sm text-gray-600">
            Connectez-vous pour accéder à votre espace personnel
          </p>
        </div>

        <LoginForm />

        <div className="text-center space-y-4">
          <p className="text-sm text-gray-600">
            Pas encore de compte ?{' '}
            <Link to="/register" className="font-medium text-blue-600 hover:text-blue-500">
              Créez-en un maintenant
            </Link>
          </p>
          <p className="text-sm text-gray-600">
            <Link to="/forgot-password" className="font-medium text-blue-600 hover:text-blue-500">
              Mot de passe oublié ?
            </Link>
          </p>
        </div>
      </div>
    </div>
  );
};

export default LoginPage; 