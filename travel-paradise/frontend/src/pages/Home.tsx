import React from 'react';
import { Link } from 'react-router-dom';
import { FaMapMarkedAlt, FaUsers, FaChartLine, FaCalendarAlt } from 'react-icons/fa';

const Home: React.FC = () => {
  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <div className="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
          <div className="text-center">
            <h1 className="text-4xl md:text-6xl font-bold mb-6">
              Découvrez le Monde avec Travel Paradise
            </h1>
            <p className="text-xl md:text-2xl mb-8">
              Votre partenaire de confiance pour des visites guidées inoubliables
            </p>
            <div className="flex justify-center space-x-4">
              <Link
                to="/destinations"
                className="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition duration-300"
              >
                Explorer les Destinations
              </Link>
              <Link
                to="/register"
                className="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300"
              >
                Rejoignez-nous
              </Link>
            </div>
          </div>
        </div>
      </div>

      {/* Features Section */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div className="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
            <div className="text-blue-600 mb-4">
              <FaMapMarkedAlt className="h-12 w-12" />
            </div>
            <h3 className="text-xl font-semibold mb-2">Destinations Uniques</h3>
            <p className="text-gray-600">
              Explorez des lieux extraordinaires avec nos guides experts
            </p>
          </div>

          <div className="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
            <div className="text-blue-600 mb-4">
              <FaUsers className="h-12 w-12" />
            </div>
            <h3 className="text-xl font-semibold mb-2">Guides Professionnels</h3>
            <p className="text-gray-600">
              Des guides passionnés et expérimentés à votre service
            </p>
          </div>

          <div className="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
            <div className="text-blue-600 mb-4">
              <FaChartLine className="h-12 w-12" />
            </div>
            <h3 className="text-xl font-semibold mb-2">Statistiques en Temps Réel</h3>
            <p className="text-gray-600">
              Suivez vos visites et performances en direct
            </p>
          </div>

          <div className="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
            <div className="text-blue-600 mb-4">
              <FaCalendarAlt className="h-12 w-12" />
            </div>
            <h3 className="text-xl font-semibold mb-2">Planification Flexible</h3>
            <p className="text-gray-600">
              Organisez vos visites selon vos disponibilités
            </p>
          </div>
        </div>
      </div>

      {/* Popular Destinations */}
      <div className="bg-gray-100 py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-bold text-center mb-12">Destinations Populaires</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition duration-300">
              <img
                src="https://images.unsplash.com/photo-1502602898657-3e91760cbb34?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                alt="Paris"
                className="w-full h-48 object-cover"
              />
              <div className="p-6">
                <h3 className="text-xl font-semibold mb-2">Paris</h3>
                <p className="text-gray-600 mb-4">
                  La ville lumière vous attend avec ses monuments emblématiques
                </p>
                <Link
                  to="/destinations"
                  className="text-blue-600 font-semibold hover:text-blue-800"
                >
                  Découvrir →
                </Link>
              </div>
            </div>

            <div className="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition duration-300">
              <img
                src="https://images.unsplash.com/photo-1560179707-f14e90ef3623?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                alt="Lyon"
                className="w-full h-48 object-cover"
              />
              <div className="p-6">
                <h3 className="text-xl font-semibold mb-2">Lyon</h3>
                <p className="text-gray-600 mb-4">
                  Découvrez la capitale gastronomique de la France
                </p>
                <Link
                  to="/destinations"
                  className="text-blue-600 font-semibold hover:text-blue-800"
                >
                  Découvrir →
                </Link>
              </div>
            </div>

            <div className="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition duration-300">
              <img
                src="https://images.unsplash.com/photo-1560179707-f14e90ef3623?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                alt="Marseille"
                className="w-full h-48 object-cover"
              />
              <div className="p-6">
                <h3 className="text-xl font-semibold mb-2">Marseille</h3>
                <p className="text-gray-600 mb-4">
                  Explorez la cité phocéenne et ses trésors méditerranéens
                </p>
                <Link
                  to="/destinations"
                  className="text-blue-600 font-semibold hover:text-blue-800"
                >
                  Découvrir →
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Call to Action */}
      <div className="bg-blue-600 text-white py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl font-bold mb-4">Prêt à Commencer Votre Aventure ?</h2>
          <p className="text-xl mb-8">
            Rejoignez-nous dès aujourd'hui et commencez à explorer le monde
          </p>
          <Link
            to="/register"
            className="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition duration-300"
          >
            Créer un Compte
          </Link>
        </div>
      </div>
    </div>
  );
};

export default Home; 