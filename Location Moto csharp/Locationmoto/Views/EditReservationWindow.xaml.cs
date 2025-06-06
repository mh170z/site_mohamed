﻿using Locationmoto.Models;
using Locationmoto.Services;
using Locationmoto.ViewModels;
using System;
using System.Windows;

namespace Locationmoto.Views
{
    /// <summary>
    /// Logique d'interaction pour EditReservationWindow.xaml
    /// </summary>
    public partial class EditReservationWindow : Window
    {
        private readonly LocationVM _locationVM;
        private readonly Location _location;
        private readonly LocationMoto _locationMoto;
        private decimal _price;

        /// <summary>
        /// Initialise une nouvelle instance de la fenêtre de modification de réservation
        /// avec la location et la moto de location spécifiées.
        /// </summary>
        /// <param name="location">La location à modifier</param>
        /// <param name="locationMoto">La moto de location associée</param>
        public EditReservationWindow(Location location, LocationMoto locationMoto)
        {
            InitializeComponent();
            _locationVM = new LocationVM();
            _location = location;
            _locationMoto = locationMoto;
            InitializeUI();
        }

        /// <summary>
        /// Initialise l'interface utilisateur avec les informations
        /// de la location et de la moto sélectionnées.
        /// </summary>
        private void InitializeUI()
        {
            MotoImage.Source = ImageService.LoadImage(_locationMoto.moto.image);
            NomMotoText.Text = _locationMoto.moto.nomMoto;
            QuantiteText.Text = _locationMoto.quantite.ToString();

            DateDebutPicker.DisplayDateStart = DateTime.Today;
            DateDebutPicker.SelectedDate = _location.dateDebutLocation;
            DateFinPicker.DisplayDateStart = DateTime.Today;
            DateFinPicker.SelectedDate = _location.dateFinLocation;

            CalculatePrixTotal();
        }

        /// <summary>
        /// Calcule le prix total de la location en fonction des dates sélectionnées et de la quantité.
        /// Met à jour l'affichage du prix total.
        /// </summary>
        private void CalculatePrixTotal()
        {
            if (DateDebutPicker.SelectedDate.HasValue && DateFinPicker.SelectedDate.HasValue)
            {
                var debut = DateDebutPicker.SelectedDate.Value;
                var fin = DateFinPicker.SelectedDate.Value;

                if (fin >= debut)
                {
                    int nombreJours = (fin - debut).Days + 1;
                    _price = _locationMoto.moto.tarif * nombreJours * _locationMoto.quantite;
                    PrixTotalText.Text = _price.ToString("C");
                }
            }
        }

        /// <summary>
        /// Gère le changement de date dans les DatePicker.
        /// Recalcule le prix total lorsqu'une date est modifiée.
        /// </summary>
        /// <param name="sender">Le DatePicker qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de changement de sélection</param>
        private void DatePicker_SelectedDateChanged(object sender, System.Windows.Controls.SelectionChangedEventArgs e)
        {
            CalculatePrixTotal();
        }

        /// <summary>
        /// Gère le clic sur le bouton "Confirmer" pour valider les modifications de la réservation.
        /// Vérifie que toutes les entrées sont valides et met à jour la location.
        /// </summary>
        /// <param name="sender">Le bouton qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de clic</param>
        private void ConfirmerButton_Click(object sender, RoutedEventArgs e)
        {
            if (!DateDebutPicker.SelectedDate.HasValue || !DateFinPicker.SelectedDate.HasValue)
            {
                MessageBox.Show("Veuillez sélectionner les dates de début et de fin.");
                return;
            }

            if (DateFinPicker.SelectedDate < DateDebutPicker.SelectedDate)
            {
                MessageBox.Show("La date de fin doit être postérieure ou égale à la date de début.");
                return;
            }

            if (DateDebutPicker.SelectedDate < DateTime.Today)
            {
                MessageBox.Show("La date de début ne peut pas être antérieure à aujourd'hui.");
                return;
            }

            try
            {
                if (_locationVM.UpdateLocation(_location, DateDebutPicker.SelectedDate.Value, DateFinPicker.SelectedDate.Value))
                {
                    MessageBox.Show("Réservation modifiée avec succès !");
                    DialogResult = true;
                    Close();
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erreur lors de la modification : {ex.Message}");
            }
        }

        /// <summary>
        /// Gère le clic sur le bouton "Annuler".
        /// Ferme la fenêtre sans sauvegarder les modifications.
        /// </summary>
        /// <param name="sender">Le bouton qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de clic</param>
        private void AnnulerButton_Click(object sender, RoutedEventArgs e)
        {
            DialogResult = false;
            Close();
        }
    }
}
