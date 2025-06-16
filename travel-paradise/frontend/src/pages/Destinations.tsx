import React from 'react';

const Destinations: React.FC = () => {
  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-6">Nos Destinations</h1>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {/* Vous pouvez ajouter ici une liste de destinations avec des cartes */}
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold mb-3">Paris</h2>
          <p className="text-gray-600">
            Découvrez la ville lumière avec nos guides experts.
          </p>
        </div>
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold mb-3">Lyon</h2>
          <p className="text-gray-600">
            Explorez la capitale gastronomique de la France.
          </p>
        </div>
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold mb-3">Marseille</h2>
          <p className="text-gray-600">
            Visitez la plus ancienne ville de France.
          </p>
        </div>
      </div>
    </div>
  );
};

export default Destinations; 