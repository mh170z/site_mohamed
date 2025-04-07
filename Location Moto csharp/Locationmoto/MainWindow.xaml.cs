using System;
using System.Collections.Generic;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using LocationMoto.Models;  // Remplacement de LocationMontagne.Models
using LocationMoto.Services; // Remplacement de LocationMontagne.Services
using LocationMoto.ViewModels; // Remplacement de LocationMontagne.ViewModels
using LocationMoto.Views; // Remplacement de LocationMontagne.Views

namespace LocationMoto  // Remplacement de LocationMontagne
{
    /// <summary>
    /// Logique d'interaction pour MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window
    {
        private CategorieVM categorieVM;
        private ArticleVM articleVM;
        private User currentUser;

        /// <summary>
        /// Initialise une nouvelle instance de la fenêtre principale
        /// avec l'utilisateur connecté (ou null pour un utilisateur invité).
        /// </summary>
        /// <param name="user">L'utilisateur connecté, ou null si mode invité</param>
        public MainWindow(User user = null)
        {
            InitializeComponent();
            categorieVM = new CategorieVM();
            articleVM = new ArticleVM();
            currentUser = user;
            InitializeCategories();
            UpdateUIForUser();
        }

        /// <summary>
        /// Met à jour l'interface utilisateur en fonction de l'état de connexion de l'utilisateur.
        /// Affiche soit les boutons d'authentification, soit les boutons utilisateur.
        /// </summary>
        private void UpdateUIForUser()
        {
            if (currentUser != null) 
            {
                AuthButtons.Visibility = Visibility.Collapsed;
                UserButtons.Visibility = Visibility.Visible;
            }
            else
            {
                AuthButtons.Visibility = Visibility.Visible;
                UserButtons.Visibility = Visibility.Collapsed;
            }
        }

        /// <summary>
        /// Gère la navigation vers la fenêtre des réservations de l'utilisateur.
        /// </summary>
        /// <param name="sender">Le contrôle qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de clic</param>
        private void NavigateToReservations(object sender, RoutedEventArgs e)
        {
            ReservationWindow reservationWindow = new ReservationWindow(currentUser);
            reservationWindow.Show();
            this.Close();
        }

        /// <summary>
        /// Gère la déconnexion de l'utilisateur.
        /// Réinitialise l'utilisateur courant et recharge la fenêtre principale en mode invité.
        /// </summary>
        /// <param name="sender">Le contrôle qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de clic</param>
        private void Deconnexion(object sender, RoutedEventArgs e)
        {
            currentUser = null;
            MainWindow newWindow = new MainWindow();
            newWindow.Show();
            this.Close();
        }

        /// <summary>
        /// Affiche une collection d'articles (motos) dans l'interface utilisateur.
        /// Crée une carte pour chaque moto et les ajoute au panneau d'articles.
        /// </summary>
        /// <param name="articles">La collection d'articles à afficher</param>
        private void DisplayArticles(IEnumerable<Article> articles)
        {
            ArticlesWrapPanel.Children.Clear();

            foreach (var article in articles)
            {
                var card = CreateArticleCard(article);
                ArticlesWrapPanel.Children.Add(card);
            }
        }

        /// <summary>
        /// Crée une carte d'interface utilisateur pour un article spécifique (moto).
        /// Inclut l'image, les informations et les boutons d'action.
        /// </summary>
        /// <param name="article">L'article pour lequel créer une carte</param>
        /// <returns>Un contrôle Border contenant la carte d'article</returns>
        private Border CreateArticleCard(Article article)
        {
            var card = new Border
            {
                Style = (Style)FindResource("ArticleCardStyle")
            };

            var mainStack = new StackPanel
            {
                Margin = new Thickness(0)
            };

            var imageContainer = new Border
            {
                Height = 180,
                CornerRadius = new CornerRadius(8),
                Margin = new Thickness(0, 0, 0, 16),
                ClipToBounds = true //image arrondis
            };

            // Image
            var image = new Image
            {
                Source = ImageService.LoadImage(article.image),
                Style = (Style)FindResource("ArticleImageStyle")
            };

            imageContainer.Child = image;

            // Grille pour image + badge dispo
            var imageGrid = new Grid();
            imageGrid.Children.Add(imageContainer);

            // Disponibilité
            var stockBadge = new Border
            {
                Style = (Style)FindResource("BadgeStyle"),
                Background = article.quantiteStock > 0
                    ? (SolidColorBrush)FindResource("SuccessColor")
                    : (SolidColorBrush)FindResource("DangerColor")
            };

            var badgeText = new TextBlock
            {
                Text = article.quantiteStock > 0 ? "Disponible" : "Rupture de stock",
                Foreground = Brushes.White,
                FontSize = 12,
                FontWeight = FontWeights.SemiBold
            };

            stockBadge.Child = badgeText;
            imageGrid.Children.Add(stockBadge);

            // Catégorie
            var categoryText = new TextBlock
            {
                Text = article.categorie.nomCategorie,
                Foreground = (SolidColorBrush)FindResource("SecondaryColor"),
                FontSize = 14,
                FontWeight = FontWeights.SemiBold,
                Margin = new Thickness(0, 0, 0, 8)
            };

            // Nom 
            var nomArticle = new TextBlock
            {
                Text = article.nomArticle,
                FontSize = 18,
                FontWeight = FontWeights.Bold,
                TextTrimming = TextTrimming.CharacterEllipsis,
                Foreground = (SolidColorBrush)FindResource("TextPrimaryColor"),
                Margin = new Thickness(0, 0, 0, 8)
            };

            // Prix 
            var prix = new TextBlock
            {
                Text = $"{article.tarif:C}/jour",
                Style = (Style)FindResource("PriceStyle")
            };

            // Stock
            var stockInfo = new TextBlock
            {
                Text = $"En stock: {article.quantiteStock}",
                Foreground = article.quantiteStock > 0
                    ? (SolidColorBrush)FindResource("SuccessColor")
                    : (SolidColorBrush)FindResource("DangerColor"),
                FontWeight = FontWeights.Medium,
                Margin = new Thickness(0, 0, 0, 16)
            };

            var buttonsPanel = new StackPanel
            {
                Orientation = Orientation.Horizontal,
                HorizontalAlignment = HorizontalAlignment.Center,
                Margin = new Thickness(0, 8, 0, 0)
            };

            // Boutons louer + détails
            var detailsButton = new Button
            {
                Content = "Détails",
                Style = (Style)FindResource("OutlineButtonStyle"),
                Width = 120,
                Height = 40,
                Margin = new Thickness(0, 0, 8, 0)
            };
            detailsButton.Click += (s, e) => ShowArticleDetails(article);

            var rentButton = new Button
            {
                Content = "Louer",
                Style = (Style)FindResource("PrimaryButtonStyle"),
                Width = 120,
                Height = 40
            };

            // Article indispo
            if (article.quantiteStock == 0)
            {
                rentButton.IsEnabled = false;
                rentButton.ToolTip = "Cet article n'est pas disponible actuellement";
            }
            else
            {
                rentButton.Click += (s, e) => {
                    if (currentUser != null)
                    {
                        AddReservationWindow reservationWindow = new AddReservationWindow(article, currentUser);
                        reservationWindow.Show();
                        this.Close();
                    }
                    else
                    {
                        RedirectToConnection();
                    }
                };
            }

            // Boutons
            buttonsPanel.Children.Add(detailsButton);
            buttonsPanel.Children.Add(rentButton);

            // Assemblage des éléments
            mainStack.Children.Add(imageGrid);
            mainStack.Children.Add(categoryText);
            mainStack.Children.Add(nomArticle);
            mainStack.Children.Add(prix);
            mainStack.Children.Add(stockInfo);
            mainStack.Children.Add(buttonsPanel);

            card.Child = mainStack;

            return card;
        }

                /// <summary>
        /// Redirige l'utilisateur vers la page de connexion si l'utilisateur n'est pas connecté.
        /// </summary>
        private void RedirectToConnection()
        {
            LoginWindow loginWindow = new LoginWindow();
            loginWindow.Show();
            this.Close();
        }

        /// <summary>
        /// Affiche les détails d'un article (moto).
        /// </summary>
        /// <param name="article">L'article dont afficher les détails</param>
        private void ShowArticleDetails(Article article)
        {
            var detailsWindow = new ArticleDetailsWindow(article);
            detailsWindow.Show();
            this.Close();
        }

        /// <summary>
        /// Initialise les catégories d'articles (motos) et les affiche.
        /// </summary>
        private void InitializeCategories()
        {
            try
            {
                var categories = categorieVM.GetCategories();  // Récupère les catégories de motos
                CategoriesListBox.ItemsSource = categories;
                CategoriesListBox.DisplayMemberPath = "NomCategorie";  // Affiche le nom de chaque catégorie
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erreur lors du chargement des catégories : {ex.Message}", "Erreur", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        /// <summary>
        /// Gère l'événement de sélection d'une catégorie d'article (moto) dans la liste.
        /// Affiche les articles correspondant à la catégorie sélectionnée.
        /// </summary>
        /// <param name="sender">Le contrôle qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de sélection</param>
        private void CategorySelectionChanged(object sender, SelectionChangedEventArgs e)
        {
            try
            {
                var selectedCategory = (Categorie)CategoriesListBox.SelectedItem;

                if (selectedCategory != null)
                {
                    var articles = articleVM.GetArticlesByCategory(selectedCategory.Id);  // Récupère les motos de la catégorie
                    DisplayArticles(articles);  // Affiche les motos de la catégorie sélectionnée
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erreur lors du chargement des articles : {ex.Message}", "Erreur", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        /// <summary>
        /// Ouvre la fenêtre d'inscription si l'utilisateur n'est pas connecté.
        /// </summary>
        private void OpenSignupWindow()
        {
            if (currentUser == null)
            {
                SignUpWindow signUpWindow = new SignUpWindow();
                signUpWindow.Show();
                this.Close();
            }
            else
            {
                MessageBox.Show("Vous êtes déjà connecté.", "Info", MessageBoxButton.OK, MessageBoxImage.Information);
            }
        }

        /// <summary>
        /// Gère la recherche d'articles (motos) en fonction du terme de recherche saisi par l'utilisateur.
        /// </summary>
        /// <param name="sender">Le contrôle qui déclenche l'événement</param>
        /// <param name="e">Les données associées à l'événement de recherche</param>
        private void SearchArticles(object sender, RoutedEventArgs e)
        {
            string searchTerm = SearchTextBox.Text.Trim();

            if (!string.IsNullOrEmpty(searchTerm))
            {
                var searchResults = articleVM.SearchArticles(searchTerm);  // Recherche les motos en fonction du terme de recherche
                DisplayArticles(searchResults);  // Affiche les résultats de la recherche
            }
            else
            {
                MessageBox.Show("Veuillez entrer un terme de recherche.", "Avertissement", MessageBoxButton.OK, MessageBoxImage.Warning);
            }
        }

        /// <summary>
        /// Affiche les articles (motos) disponibles à la location sur la page principale.
        /// </summary>
        private void ShowAvailableArticles()
        {
            try
            {
                var availableArticles = articleVM.GetAvailableArticles();  // Récupère les motos disponibles
                DisplayArticles(availableArticles);  // Affiche les motos disponibles
            }
            catch (Exception ex)
            {
                MessageBox.Show("Erreur lors de l'affichage des articles disponibles : {ex.Message}", "Erreur", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }
    }
}
