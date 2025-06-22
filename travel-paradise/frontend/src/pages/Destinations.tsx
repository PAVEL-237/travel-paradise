import React, { useState } from 'react';
import { FaSearch, FaMapMarkerAlt, FaStar, FaClock, FaUsers } from 'react-icons/fa';

interface Destination {
  id: number;
  name: string;
  description: string;
  image: string;
  rating: number;
  duration: string;
  price: string;
  visitors: number;
}

const destinations: Destination[] = [
  {
    id: 1,
    name: 'Paris',
    description: 'La ville lumière vous attend avec ses monuments emblématiques et son charme unique.',
    image: 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
    rating: 4.8,
    duration: '3 jours',
    price: 'À partir de 299€',
    visitors: 1500
  },
  {
    id: 2,
    name: 'Lyon',
    description: 'Découvrez la capitale gastronomique de la France et son riche patrimoine historique.',
    image: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
    rating: 4.6,
    duration: '2 jours',
    price: 'À partir de 199€',
    visitors: 1200
  },
  {
    id: 3,
    name: 'Marseille',
    description: 'Explorez la cité phocéenne et ses trésors méditerranéens.',
    image: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
    rating: 4.5,
    duration: '2 jours',
    price: 'À partir de 179€',
    visitors: 1000
  }
];

const Destinations: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('');

  const filteredDestinations = destinations.filter(destination =>
    destination.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-gray-50 py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">
            Nos Destinations
          </h1>
          <p className="text-xl text-gray-600">
            Découvrez nos destinations les plus populaires
          </p>
        </div>

        {/* Search Bar */}
        <div className="max-w-xl mx-auto mb-12">
          <div className="relative">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <FaSearch className="h-5 w-5 text-gray-400" />
            </div>
            <input
              type="text"
              className="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Rechercher une destination..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
        </div>

        {/* Destinations Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {filteredDestinations.map((destination) => (
            <div
              key={destination.id}
              className="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition duration-300"
            >
              <div className="relative">
                <img
                  src={destination.image}
                  alt={destination.name}
                  className="w-full h-48 object-cover"
                />
                <div className="absolute top-4 right-4 bg-white px-3 py-1 rounded-full flex items-center space-x-1">
                  <FaStar className="text-yellow-400" />
                  <span className="font-semibold">{destination.rating}</span>
                </div>
              </div>
              <div className="p-6">
                <div className="flex items-center space-x-2 mb-2">
                  <FaMapMarkerAlt className="text-blue-600" />
                  <h3 className="text-xl font-semibold text-gray-900">
                    {destination.name}
                  </h3>
                </div>
                <p className="text-gray-600 mb-4">{destination.description}</p>
                <div className="grid grid-cols-3 gap-4 mb-4">
                  <div className="flex items-center space-x-1">
                    <FaClock className="text-gray-400" />
                    <span className="text-sm text-gray-600">{destination.duration}</span>
                  </div>
                  <div className="flex items-center space-x-1">
                    <FaUsers className="text-gray-400" />
                    <span className="text-sm text-gray-600">{destination.visitors} visiteurs</span>
                  </div>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-lg font-semibold text-blue-600">
                    {destination.price}
                  </span>
                  <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                    Réserver
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default Destinations; 