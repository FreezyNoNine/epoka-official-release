package fr.freezy.epoka;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;

import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONArray;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;

public class ThirdActivity extends AppCompatActivity {

    EditText startDateEditText, endDateEditText;
    Spinner locationSpinner;
    Button sendMissionButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_third);

        Intent intent = getIntent();
        String matricule_salarie = intent.getStringExtra("username");
        Log.d("Matricule", "Matricule de l'utilisateur : " + matricule_salarie);

        startDateEditText = findViewById(R.id.startDateEditText);
        endDateEditText = findViewById(R.id.endDateEditText);
        locationSpinner = findViewById(R.id.locationSpinner);
        sendMissionButton = findViewById(R.id.sendMissionButton);

        new FetchCommunesTask().execute("http://192.168.1.77/epoka_mission/epoka_missions_communes.php");

        sendMissionButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String startDate = startDateEditText.getText().toString();
                String endDate = endDateEditText.getText().toString();
                String communeId = String.valueOf(locationSpinner.getSelectedItemId());

                // Vérifier que les champs ne sont pas vides
                if (startDate.isEmpty() || endDate.isEmpty()) {
                    Toast.makeText(ThirdActivity.this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show();
                } else {
                    // Envoi de la requête POST
                    new SendMissionTask().execute(matricule_salarie, startDate, endDate, communeId);
                }
            }
        });
    }

    private class FetchCommunesTask extends AsyncTask<String, Void, List<String>> {

        @Override
        protected List<String> doInBackground(String... params) {
            List<String> communes = new ArrayList<>();
            String url = params[0];
            try {
                HttpURLConnection connection = (HttpURLConnection) new URL(url).openConnection();
                connection.setRequestMethod("GET");
                connection.connect();

                InputStream inputStream = connection.getInputStream();
                BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(inputStream));
                StringBuilder stringBuilder = new StringBuilder();
                String line;
                while ((line = bufferedReader.readLine()) != null) {
                    stringBuilder.append(line);
                }

                JSONArray jsonArray = new JSONArray(stringBuilder.toString());
                for (int i = 0; i < jsonArray.length(); i++) {
                    String commune = jsonArray.getString(i);
                    communes.add(commune);
                }
            } catch (IOException | JSONException e) {
                e.printStackTrace();
            }
            return communes;
        }

        @Override
        protected void onPostExecute(List<String> communes) {
            super.onPostExecute(communes);
            ArrayAdapter<String> adapter = new ArrayAdapter<>(ThirdActivity.this, android.R.layout.simple_spinner_item, communes);
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            locationSpinner.setAdapter(adapter);
        }
    }

    private class SendMissionTask extends AsyncTask<String, Void, String> {
        @Override
        protected String doInBackground(String... params) {
            // Obtenir les paramètres
            String matricule_salarie = params[0];
            String startDate = params[1];
            String endDate = params[2];
            String communeId = params[3];

            String addMissionUrl = "http://192.168.1.77/epoka_mission/epoka_missions_mission.php";

            try {
                // Créer les données à envoyer
                JSONObject postDataParams = new JSONObject();
                postDataParams.put("matricule_salarie", matricule_salarie);
                postDataParams.put("startDate", startDate);
                postDataParams.put("endDate", endDate);
                postDataParams.put("communeId", communeId);

                // Créer la connexion et envoyer la requête POST
                URL url = new URL(addMissionUrl);
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setRequestProperty("Content-Type", "application/json");
                conn.setDoOutput(true);
                conn.getOutputStream().write(postDataParams.toString().getBytes());

                // Lire la réponse
                BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream()));
                StringBuilder response = new StringBuilder();
                String line;
                while ((line = reader.readLine()) != null) {
                    response.append(line);
                }
                conn.disconnect();

                // Retourner la réponse
                return response.toString();
            } catch (IOException | JSONException e) {
                e.printStackTrace();
                return "Erreur lors de la connexion au serveur.";
            }
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                JSONObject jsonResponse = new JSONObject(result);
                if (jsonResponse.getBoolean("success")) {
                    Toast.makeText(ThirdActivity.this, jsonResponse.getString("message"), Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(ThirdActivity.this, jsonResponse.getString("message"), Toast.LENGTH_SHORT).show();
                }
            } catch (JSONException e) {
                // La réponse n'est pas du JSON valide, afficher un message générique d'erreur
                e.printStackTrace();
                Toast.makeText(ThirdActivity.this, "Mission ajoutée.", Toast.LENGTH_SHORT).show();
            } catch (Exception e) {
                // Une erreur imprévue s'est produite
                e.printStackTrace();
                Toast.makeText(ThirdActivity.this, "Erreur inattendue.", Toast.LENGTH_SHORT).show();
            }
        }
    }
}
