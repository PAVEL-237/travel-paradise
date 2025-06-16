import React from 'react';

const About: React.FC = () => {
  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-6">À propos de Travel Paradise</h1>
      <div className="prose max-w-none">
        <p className="mb-4">
          Bienvenue sur Travel Paradise, votre plateforme de gestion de visites guidées.
        </p>
        <p className="mb-4">
          Notre mission est de faciliter l'organisation et la gestion des visites guidées
          pour les guides touristiques et les administrateurs.
        </p>
        <p className="mb-4">
          Nous offrons une solution complète pour :
        </p>
        <ul className="list-disc pl-6 mb-4">
          <li>Gérer les visites guidées</li>
          <li>Suivre les statistiques</li>
          <li>Administrer les guides et les utilisateurs</li>
          <li>Planifier et organiser les destinations</li>
        </ul>
      </div>
    </div>
  );
};

export default About; 