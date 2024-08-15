package fr.freezy.epoka;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;

public class MainActivity extends AppCompatActivity {

    private static final String TAG = "MainActivity";
    static final String PHP_URL = "http://192.168.1.77/epoka_mission/epoka_missions_login.php";
    EditText usernameEditText, passwordEditText;
    Button loginButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        usernameEditText = findViewById(R.id.username);
        passwordEditText = findViewById(R.id.password);
        loginButton = findViewById(R.id.login_button);

        loginButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String username = usernameEditText.getText().toString();
                String password = passwordEditText.getText().toString();

                if (TextUtils.isEmpty(username) || TextUtils.isEmpty(password)) {
                    Toast.makeText(MainActivity.this, "Veuillez remplir tous les champs", Toast.LENGTH_LONG).show();
                } else {
                    // Exécuter la tâche asynchrone pour vérifier les informations d'identification
                    new VerifyCredentialsTask().execute(username, password);
                }
            }
        });
    }

    private class VerifyCredentialsTask extends AsyncTask<String, Void, Boolean> {

        @Override
        protected Boolean doInBackground(String... params) {
            String username = params[0];
            String password = params[1];

            try {
                URL url = new URL(PHP_URL + "?matricule=" + username); // Passer le matricule en paramètre GET
                HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                connection.setRequestMethod("GET");
                connection.connect();

                InputStream inputStream = connection.getInputStream();
                BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(inputStream));
                StringBuilder stringBuilder = new StringBuilder();
                String line;
                while ((line = bufferedReader.readLine()) != null) {
                    stringBuilder.append(line);
                }

                // Convertir la réponse JSON en tableau d'objets JSON
                JSONArray jsonArray = new JSONArray(stringBuilder.toString());

                // Vérifier si la réponse contient au moins un objet JSON
                if (jsonArray.length() > 0) {
                    JSONObject jsonObject = jsonArray.getJSONObject(0);
                    String dbPassword = jsonObject.getString("password");

                    // Vérifier si le mot de passe correspond
                    return password.equals(dbPassword);
                }

            } catch (IOException | JSONException e) {
                Log.e(TAG, "Erreur lors de la vérification des informations d'identification: " + e.getMessage());
            }

            return false;
        }

        @Override
        protected void onPostExecute(Boolean isValidCredentials) {
            super.onPostExecute(isValidCredentials);

            if (isValidCredentials) {
                // Appeler GetUserInfoTask pour récupérer les informations du salarié
                new GetUserInfoTask().execute(usernameEditText.getText().toString());
            } else {
                // Les identifiants sont incorrects
                Toast.makeText(MainActivity.this, "Nom d'utilisateur ou mot de passe incorrect", Toast.LENGTH_LONG).show();
            }
        }
    }

    // Ajouter une classe AsyncTask pour récupérer les informations du salarié
    private class GetUserInfoTask extends AsyncTask<String, Void, String[]> {

        @Override
        protected String[] doInBackground(String... params) {
            String matricule = params[0];
            String[] userInfo = null;
            HttpURLConnection urlConnection = null;
            BufferedReader reader = null;

            try {
                // Construire l'URL avec le matricule
                String urlString = "http://192.168.1.77/epoka_mission/epoka_missions_names.php?matricule=" + matricule;
                URL url = new URL(urlString);

                // Ouvrir la connexion HTTP
                urlConnection = (HttpURLConnection) url.openConnection();
                urlConnection.setRequestMethod("GET");
                urlConnection.connect();

                // Lire la réponse du serveur
                InputStream inputStream = urlConnection.getInputStream();
                StringBuilder buffer = new StringBuilder();
                if (inputStream == null) {
                    // Aucune donnée reçue
                    return null;
                }
                reader = new BufferedReader(new InputStreamReader(inputStream));

                String line;
                while ((line = reader.readLine()) != null) {
                    buffer.append(line).append("\n");
                }

                if (buffer.length() == 0) {
                    // La réponse est vide
                    return null;
                }

                // Traiter la réponse JSON
                String jsonResponse = buffer.toString();
                JSONObject jsonObject = new JSONObject(jsonResponse);
                String nom = jsonObject.getString("nom");
                String prenom = jsonObject.getString("prenom");

                userInfo = new String[]{nom, prenom};
            } catch (IOException | JSONException e) {
                Log.e(TAG, "Erreur lors de la récupération du nom et du prénom: " + e.getMessage());
            } finally {
                // Fermer la connexion et le lecteur
                if (urlConnection != null) {
                    urlConnection.disconnect();
                }
                if (reader != null) {
                    try {
                        reader.close();
                    } catch (IOException e) {
                        Log.e(TAG, "Erreur lors de la fermeture du flux de lecture: " + e.getMessage());
                    }
                }
            }

            return userInfo;
        }

        @Override
        protected void onPostExecute(String[] userInfo) {
            super.onPostExecute(userInfo);

            if (userInfo != null && userInfo.length >= 2) {
                // Récupérer les informations du salarié
                String nom = userInfo[0];
                String prenom = userInfo[1];

                // Rediriger vers SecondActivity et passer les informations
                Intent intent = new Intent(MainActivity.this, SecondActivity.class);
                intent.putExtra("nom", nom);
                intent.putExtra("prenom", prenom);
                startActivity(intent);
                finish(); // Terminer l'activité actuelle pour ne pas pouvoir revenir en arrière avec le bouton Retour
            } else {
                // Gérer le cas où les informations ne sont pas disponibles
                Toast.makeText(MainActivity.this, "Erreur lors de la récupération des informations du salarié", Toast.LENGTH_LONG).show();
            }
        }
    }
}
