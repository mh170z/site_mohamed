﻿using MySqlConnector;

namespace LocationMontagne.Services
{
    /// <summary>
    /// Classe de l'objet Database permettant une connexion Mysql à la base de données.
    /// </summary>
    public class Database
    {
        private string host = "localhost";
        private string username = "UserLocationmoto";
        private string password = "PasswordLocationmoto";
        private string dbName = "Locationmoto";

        public static MySqlConnection getConnection()
        {
            var database = new Database();
            var connection = new MySqlConnection("Server=" + database.host + ";Database=" + database.dbName + ";userid=" + database.username + ";password=" + database.password + ";");
            connection.Open();
            return connection;
        }
    }
}
