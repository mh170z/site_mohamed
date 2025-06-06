﻿using LocationMoto.Models;
using LocationMoto.Services;
using LocationMoto.ViewModels;
using System;
using System.Text.RegularExpressions;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Input;

namespace LocationMoto.Views
{
    /// <summary>
    /// Logique d'interaction pour AddReservationWindow.xaml
    /// </summary>
    public partial class AddReservationWindow : Window
    {
        private readonly Moto _moto;
        private readonly User _user;
        private readonly LocationVM _locationVM;
        private decimal _price;

        /// <summary>
        /// Initialise une nouvelle instance de la fenêtre d'ajout de réservation 
        /// avec la moto à réserver et l'utilisateur qui effectue la réservation.
        /// </summary>
        /// <param name="moto">La moto à réserver</param>
        /// <param name="user">L'utilisateur qui effectue la réservation</param>
        public AddReservationWindow(Moto moto, User user)
        {
            _moto = moto;
            _user = user;
            _locationVM = new LocationVM();
            InitializeComponent();

            MotoImage.Source = ImageService.LoadImage(moto.image);
            NomMotoTextBox.Text = moto.nomMoto;
            DescriptionTextBox.Text = moto.description;
            TarifTextBox.Text = moto.tarif.ToString("C");

            DateDebutPicker.DisplayDateStart = DateTime.Today;
            DateFinPicker.DisplayDateStart = DateTime.Today;

            CalculatePrixTotal();
            DataContext = _moto;
        }

        /// <summary>
        /// Gère le changement de date dans les DatePicker.
        /// Recalcule le prix total lorsqu'une date est modifiée.
        /// </summary>
        /// <param name="sender">Le DatePicker qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de changement de sélection</param>
        private void DatePicker_SelectedDateChanged(object sender, SelectionChangedEventArgs e)
        {
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
                    int quantite = 1;
                    if (!string.IsNullOrEmpty(QuantiteTextBox.Text))
                    {
                        int.TryParse(QuantiteTextBox.Text, out quantite);
                    }

                    _price = _moto.tarif * nombreJours * quantite;
                    PrixTotalTextBox.Text = _price.ToString("C");
                }
            }
        }

        /// <summary>
        /// Gère le changement de texte dans le champ de quantité.
        /// Vérifie que la quantité demandée est valide et ne dépasse pas le stock disponible.
        /// </summary>
        /// <param name="sender">Le TextBox qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de changement de texte</param>
        private void QuantiteTextBox_TextChanged(object sender, TextChangedEventArgs e)
        {
            if (_moto == null)
            {
                MessageBox.Show("La moto n'est pas définie.", "Erreur", MessageBoxButton.OK, MessageBoxImage.Error);
                return;
            }

            if (int.TryParse(QuantiteTextBox.Text, out int quantite))
            {
                if (quantite > _moto.quantiteStock)
                {
                    QuantiteTextBox.Text = _moto.quantiteStock.ToString();
                    MessageBox.Show($"La quantité demandée excède le stock disponible ({_moto.quantiteStock} moto(s)).",
                        "Stock insuffisant", MessageBoxButton.OK, MessageBoxImage.Warning);
                }
                else if (quantite < 1)
                {
                    QuantiteTextBox.Text = "1";
                }
            }
            CalculatePrixTotal();
        }

        /// <summary>
        /// Vérifie que seuls des caractères numériques sont saisis dans le champ de quantité.
        /// </summary>
        /// <param name="sender">Le TextBox qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de composition du texte</param>
        private void NumberValidationTextBox(object sender, TextCompositionEventArgs e)
        {
            Regex regex = new Regex("[^0-9]+");
            e.Handled = regex.IsMatch(e.Text);
        }

        /// <summary>
        /// Gère le clic sur le bouton "Confirmer" pour valider la réservation.
        /// Vérifie que toutes les entrées sont valides et crée une nouvelle location.
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

            if (DateFinPicker.SelectedDate <= DateDebutPicker.SelectedDate)
            {
                MessageBox.Show("La date de fin doit être après à la date de début.");
                return;
            }

            int quantite;
            if (!int.TryParse(QuantiteTextBox.Text, out quantite) || quantite < 1)
            {
                MessageBox.Show("Veuillez entrer une quantité valide.");
                return;
            }

            if (quantite > _moto.quantiteStock)
            {
                MessageBox.Show($"Veuillez nous priez de la gêne occasionné, il n'y a que {_moto.quantiteStock} moto(s) disponible(s).");
                return;
            }

            try
            {
                var location = _locationVM.CreateLocation(
                    DateDebutPicker.SelectedDate.Value,
                    DateFinPicker.SelectedDate.Value,
                    quantite,
                    _user,
                    _moto
                );

                if (location != null)
                {
                    MessageBox.Show("Réservation confirmée avec succès !");
                    MainWindow mainWindow = new MainWindow(_user);
                    mainWindow.Show();
                    this.Close();
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erreur lors de la réservation : {ex.Message}");
            }
        }

        /// <summary>
        /// Gère le clic sur le bouton "Retour".
        /// Ferme la fenêtre de réservation et retourne à la fenêtre principale.
        /// </summary>
        /// <param name="sender">Le bouton qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de clic</param>
        private void RetourButton_Click(object sender, RoutedEventArgs e)
        {
            MainWindow mainWindow = new MainWindow(_user);
            mainWindow.Show();
            this.Close();
        }
    }
}
