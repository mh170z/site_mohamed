using LocationMoto.Models;
using LocationMoto.ViewModels;
using System.Windows;

namespace LocationMoto.Views
{
    /// <summary>
    /// Logique d'interaction pour AddTypeMotoWindow.xaml
    /// </summary>
    public partial class AddTypeMotoWindow : Window
    {
        private readonly TypeMotoVM typeMotoVM;
        public TypeMoto NouveauTypeMoto { get; private set; }

        /// <summary>
        /// Initialise une nouvelle instance de la fenêtre d'ajout de type de moto
        /// avec le ViewModel nécessaire.
        /// </summary>
        /// <param name="typeMotoVM">Le ViewModel des types de motos</param>
        public AddTypeMotoWindow(TypeMotoVM typeMotoVM)
        {
            InitializeComponent();
            this.typeMotoVM = typeMotoVM;
        }

        /// <summary>
        /// Gère le clic sur le bouton "Ajouter" pour créer un nouveau type de moto.
        /// Valide l'entrée utilisateur, crée un nouveau type de moto et le sauvegarde dans la base de données.
        /// </summary>
        /// <param name="sender">Le bouton qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de clic</param>
        private void Ajouter_Click(object sender, RoutedEventArgs e)
        {
            if (string.IsNullOrWhiteSpace(NomTypeMotoTextBox.Text))
            {
                MessageBox.Show("Veuillez saisir un nom pour le type de moto.", "Erreur", MessageBoxButton.OK, MessageBoxImage.Warning);
                return;
            }

            NouveauTypeMoto = typeMotoVM.AjouterTypeMoto(NomTypeMotoTextBox.Text);
            if (NouveauTypeMoto != null)
            {
                DialogResult = true;
                Close();
            }
        }

        /// <summary>
        /// Gère le clic sur le bouton "Annuler".
        /// Ferme la fenêtre sans sauvegarder de type de moto.
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
