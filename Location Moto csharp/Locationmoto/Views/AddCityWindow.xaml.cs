using LocationMoto.Models;
using LocationMoto.ViewModels;
using System;
using System.Windows;

namespace LocationMoto.Views
{
    /// <summary>
    /// Logique d'interaction pour AddModelMotoWindow.xaml
    /// </summary>
    public partial class AddModelMotoWindow : Window
    {
        private readonly ModelMotoVM modelMotoVM;
        public ModelMoto NouveauModeleMoto { get; private set; }

        /// <summary>
        /// Initialise une nouvelle instance de la fenêtre d'ajout de modèle de moto
        /// avec la fenêtre propriétaire et le ViewModel nécessaire.
        /// </summary>
        /// <param name="owner">La fenêtre propriétaire de cette boîte de dialogue</param>
        /// <param name="modelMotoVM">Le ViewModel des modèles de moto</param>
        public AddModelMotoWindow(Window owner, ModelMotoVM modelMotoVM)
        {
            InitializeComponent();
            this.modelMotoVM = modelMotoVM;
            Owner = owner;
        }

        /// <summary>
        /// Gère le clic sur le bouton "Ajouter" pour créer un nouveau modèle de moto.
        /// Tente d'ajouter le modèle de moto à la base de données et affiche un message d'erreur en cas d'échec.
        /// </summary>
        /// <param name="sender">Le bouton qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de clic</param>
        private void AjouterModeleMoto_Click(object sender, RoutedEventArgs e)
        {
            try
            {
                int idModeleMoto = modelMotoVM.AjouterModeleMoto(NomModeleMotoTextBox.Text, MarqueTextBox.Text, AnneeFabricationTextBox.Text);
                if (idModeleMoto != -1)
                {
                    NouveauModeleMoto = new ModelMoto(idModeleMoto, NomModeleMotoTextBox.Text, MarqueTextBox.Text, AnneeFabricationTextBox.Text);
                    DialogResult = true;
                }
            }
            catch (ArgumentException ex)
            {
                MessageBox.Show(ex.Message, "Erreur", MessageBoxButton.OK, MessageBoxImage.Warning);
            }
        }

        /// <summary>
        /// Gère le clic sur le bouton "Annuler".
        /// Ferme la fenêtre sans sauvegarder de modèle de moto.
        /// </summary>
        /// <param name="sender">Le bouton qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de clic</param>
        private void Annuler_Click(object sender, RoutedEventArgs e)
        {
            DialogResult = false;
            Close();
        }
    }
}
