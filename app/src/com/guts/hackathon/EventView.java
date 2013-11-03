package com.guts.hackathon;

import java.io.BufferedOutputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

@SuppressLint("NewApi")
public class EventView extends Activity {
	
	ImageView image = null;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_event_view);

		getActionBar().setIcon(R.drawable.event);

		Intent intent = getIntent();
		int eventID = intent.getIntExtra("EVENT_ID", 0);
		System.out.println(eventID);
		Event thisEvent = null;
		for (Event e : DataAccess.getEvents()) {
			if (e.getId() == eventID) {
				thisEvent = e;
				break;
			}
		}
		System.out.println("break");
		setTitle(thisEvent.getLocation().getName());
		((TextView) findViewById(R.id.description)).setText(thisEvent
				.getDescription());
		((TextView) findViewById(R.id.expires)).setText("Expires: "
				+ thisEvent.getExpires());
		((TextView) findViewById(R.id.by_user)).setText("By: "
				+ thisEvent.getUser_name());

		System.out.println("user");
		
		String tags = "";
		int i = 0;
		int size = thisEvent.getTags().size();
		System.out.println(size);
		while (i < size) {
			tags += thisEvent.getTags().get(i);
			if (i != size - 1)
				tags += ", ";
			i++;
		}
		System.out.printf("tags: %s\n", tags);
		((TextView) findViewById(R.id.tags)).setText("Tags: " + tags);

		

		for (Media m : thisEvent.getMedia()) {
			image = new ImageView(this);			
	        // Create an object for subclass of AsyncTask
	        GetXMLTask task = new GetXMLTask();
	        // Execute the task
	        task.execute(new String[] { m.getUrl() });

			((LinearLayout) findViewById(R.id.gallery)).addView(image);
			
		}
		System.out.println("pictures");

	}
	
    private class GetXMLTask extends AsyncTask<String, Void, Bitmap> {
        protected Bitmap doInBackground(String... urls) {
            Bitmap map = null;
            for (String url : urls) {
                map = downloadImage(url);
            }
            return map;
        }
 
        // Sets the Bitmap returned by doInBackground
        protected void onPostExecute(Bitmap result) {
            image.setImageBitmap(result);
        }
 
        // Creates Bitmap from InputStream and returns it
        private Bitmap downloadImage(String url) {
            Bitmap bitmap = null;
            InputStream stream = null;
            BitmapFactory.Options bmOptions = new BitmapFactory.Options();
            bmOptions.inSampleSize = 1;
 
            try {
                stream = getHttpConnection(url);
                bitmap = BitmapFactory.
                        decodeStream(stream, null, bmOptions);
                stream.close();
            } catch (IOException e1) {
                e1.printStackTrace();
            }
            return bitmap;
        }
 
        // Makes HttpURLConnection and returns InputStream
        private InputStream getHttpConnection(String urlString)
                throws IOException {
            InputStream stream = null;
            URL url = new URL(urlString);
            URLConnection connection = url.openConnection();
 
            try {
                HttpURLConnection httpConnection = (HttpURLConnection) connection;
                httpConnection.setRequestMethod("GET");
                httpConnection.connect();
 
                if (httpConnection.getResponseCode() == HttpURLConnection.HTTP_OK) {
                    stream = httpConnection.getInputStream();
                }
            } catch (Exception ex) {
                ex.printStackTrace();
            }
            return stream;
        }
    }


	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
