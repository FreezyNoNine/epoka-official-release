package fr.freezy.epoka;

import android.content.Context;
import android.os.AsyncTask;

public class BackgroundWorker extends AsyncTask<Void,Void,Void> {
    Context context;
    BackgroundWorker(Context ctx){
        context = ctx;
    }

    @Override
    protected Void doInBackground(Void... params){
        return null;
    }

    @Override
    protected void onPreExecute(){
        super.onPreExecute();
    }

    @Override
    protected void onPostExecute(Void aVoid){
        super.onPostExecute(aVoid);
    }

    @Override
    protected void onProgressUpdate(Void... values){
        super.onProgressUpdate(values);
    }

}