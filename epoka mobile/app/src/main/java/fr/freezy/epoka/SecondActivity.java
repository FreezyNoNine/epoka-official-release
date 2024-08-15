package fr.freezy.epoka;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;

public class SecondActivity extends AppCompatActivity {

    TextView welcomeTextView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_second);

        welcomeTextView = findViewById(R.id.welcomeTextView);

        // Récupérer les données passées depuis MainActivity
        Intent intent = getIntent();
        String nom = intent.getStringExtra("nom");
        String prenom = intent.getStringExtra("prenom");

        // Afficher le message de bienvenue avec le nom et le prénom
        welcomeTextView.setText("Bienvenue, " + prenom + " " + nom);

        Button addMissionButton = findViewById(R.id.addMissionButton);
        addMissionButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Rediriger vers ThirdActivity lors du clic sur le bouton "Ajouter une mission"
                startActivity(new Intent(SecondActivity.this, ThirdActivity.class));
            }
        });
    }

}
