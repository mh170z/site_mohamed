// Exemple de mise à jour des prix dans la méthode DisplayFilteredReservations
private void DisplayFilteredReservations()
{
    ReservationsPanel.Children.Clear();
    var filteredLocations = FilterLocations();

    foreach (var location in filteredLocations)
    {
        var articles = _locationVM.GetArticlesForLocation(location);
        foreach (var locationArticle in articles)
        {
            ReservationsPanel.Children.Add(CreateReservationCard(location, locationArticle));
        }
    }

    if (!filteredLocations.Any())
    {
        DisplayNoReservationsMessage();
    }
}

private Border CreateReservationCard(Location location, LocationArticle locationArticle)
{
    var card = new Border
    {
        Style = (Style)FindResource("ReservationCardStyle")
    };

    var mainGrid = new Grid();
    mainGrid.ColumnDefinitions.Add(new ColumnDefinition { Width = new GridLength(120) });  // Image
    mainGrid.ColumnDefinitions.Add(new ColumnDefinition { Width = new GridLength(16) });   // Espacement
    mainGrid.ColumnDefinitions.Add(new ColumnDefinition { Width = new GridLength(1, GridUnitType.Star) });  // Informations
    mainGrid.ColumnDefinitions.Add(new ColumnDefinition { Width = new GridLength(16) });   // Espacement
    mainGrid.ColumnDefinitions.Add(new ColumnDefinition { Width = new GridLength(160) });  // Boutons

    var imageContainer = new Border
    {
        Width = 120,
        Height = 120,
        CornerRadius = new CornerRadius(8),
        ClipToBounds = true,
        Background = Brushes.White
    };

    // Image
    var image = new Image
    {
        Source = ImageService.LoadImage(locationArticle.article.image),
        Stretch = Stretch.Uniform,
        Margin = new Thickness(5)
    };
    imageContainer.Child = image;
    Grid.SetColumn(imageContainer, 0);

    var infoPanel = new StackPanel { Margin = new Thickness(0) };
    Grid.SetColumn(infoPanel, 2);

    // État réservation
    var statusBadge = new Border
    {
        Background = GetStatusColor(location.etatLocation.nomEtatLocation),
        CornerRadius = new CornerRadius(4),
        Padding = new Thickness(8, 4, 8, 4),
        HorizontalAlignment = HorizontalAlignment.Left,
        Margin = new Thickness(0, 0, 0, 8)
    };
    var statusText = new TextBlock
    {
        Text = location.etatLocation.nomEtatLocation,
        Foreground = Brushes.White,
        FontSize = 12,
        FontWeight = FontWeights.SemiBold,
        VerticalAlignment = VerticalAlignment.Center,
        HorizontalAlignment = HorizontalAlignment.Center
    };
    statusBadge.Child = statusText;

    // Nom
    var titleText = new TextBlock
    {
        Text = locationArticle.article.nomArticle,
        FontSize = 18,
        FontWeight = FontWeights.Bold,
        Foreground = (SolidColorBrush)FindResource("PrimaryColor"),
        Margin = new Thickness(0, 0, 0, 8)
    };

    // Période 
    var periodText = new TextBlock
    {
        Text = $"Du {location.dateDebutLocation:dd/MM/yyyy} au {location.dateFinLocation:dd/MM/yyyy}",
        Margin = new Thickness(0, 0, 0, 8)
    };

    // Quantité
    var quantityText = new TextBlock
    {
        Text = $"Quantité : {locationArticle.quantite}",
        Margin = new Thickness(0, 0, 0, 8)
    };

    // Prix
    var priceText = new TextBlock
    {
        Text = $"Total : {CalculatePrice(location, locationArticle):C}",
        FontWeight = FontWeights.SemiBold,
        Foreground = (SolidColorBrush)FindResource("SecondaryColor"),
        Margin = new Thickness(0, 8, 0, 0)
    };

    // Ajout des éléments
    infoPanel.Children.Add(statusBadge);
    infoPanel.Children.Add(titleText);
    infoPanel.Children.Add(periodText);
    infoPanel.Children.Add(quantityText);
    infoPanel.Children.Add(priceText);

    // Boutons panel
    var buttonsPanel = new StackPanel
    {
        VerticalAlignment = VerticalAlignment.Center
    };
    Grid.SetColumn(buttonsPanel, 4);

    // Boutons
    var modifyButton = new Button
    {
        Content = "Modifier",
        Style = (Style)FindResource("PrimaryButtonStyle"),
        Width = 140,
        Height = 36,
        Margin = new Thickness(0, 0, 0, 8),
        IsEnabled = location.etatLocation.nomEtatLocation == "Payée" ||
                   location.etatLocation.nomEtatLocation == "En attente de paiement"
    };
    modifyButton.Click += (s, e) => ModifierLocation(location, locationArticle);

    var cancelButton = new Button
    {
        Content = "Annuler",
        Style = (Style)FindResource("DangerButtonStyle"),
        Width = 140,
        Height = 36,
        Margin = new Thickness(0, 0, 0, 8),
        IsEnabled = location.etatLocation.nomEtatLocation == "Payée" ||
                   location.etatLocation.nomEtatLocation == "En attente de paiement"
    };
    cancelButton.Click += (s, e) => AnnulerLocation(location);

    var invoiceButton = new Button
    {
        Content = "Voir la facture",
        Style = (Style)FindResource("OutlineButtonStyle"),
        Width = 140,
        Height = 36,
        IsEnabled = location.facture != null
    };
    invoiceButton.Click += (s, e) => VoirFacture(location, locationArticle);

    // Ajout des boutons
    buttonsPanel.Children.Add(modifyButton);
    buttonsPanel.Children.Add(cancelButton);
    buttonsPanel.Children.Add(invoiceButton);

    // Assemblage
    mainGrid.Children.Add(imageContainer);
    mainGrid.Children.Add(infoPanel);
    mainGrid.Children.Add(buttonsPanel);

    card.Child = mainGrid;

    return card;
}

// Fonction pour calculer le prix total en fonction du type de moto et de la durée
private decimal CalculatePrice(Location location, LocationArticle locationArticle)
{
    decimal pricePerDay = 0;

    // Définition des prix par type de moto
    switch (locationArticle.article.nomArticle.ToLower())
    {
        case "moto standard":
            pricePerDay = 50;  // 50€ par jour
            break;
        case "moto sportive":
            pricePerDay = 80;  // 80€ par jour
            break;
        case "moto custom":
            pricePerDay = 70;  // 70€ par jour
            break;
        case "casque":
            pricePerDay = 10;  // 10€ par jour
            break;
        case "gants":
            pricePerDay = 5;   // 5€ par jour
            break;
        case "blouson":
            pricePerDay = 15;  // 15€ par jour
            break;
        case "protection genoux":
            pricePerDay = 8;   // 8€ par jour
            break;
        default:
            pricePerDay = 20;  // Prix par défaut
            break;
    }

    // Calcul du prix total selon la durée
    var daysCount = (location.dateFinLocation - location.dateDebutLocation).Days;
    var totalPrice = pricePerDay * daysCount * locationArticle.quantite;

    return totalPrice;
}
