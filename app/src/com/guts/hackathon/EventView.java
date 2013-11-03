package com.guts.hackathon;

import java.io.IOException;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.view.Menu;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

@SuppressLint("NewApi")
public class EventView extends Activity {

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
		setTitle(thisEvent.getLocation().getName());
		((TextView) findViewById(R.id.description)).setText(thisEvent
				.getDescription());
		((TextView) findViewById(R.id.expires)).setText("Expires: "
				+ thisEvent.getExpires());
		((TextView) findViewById(R.id.by_user)).setText("By: "
				+ thisEvent.getUser_name());

		String tags = "";
		int i = 0;
		int size = thisEvent.getTags().size();
		while (i < size) {
			tags += thisEvent.getTags().get(i);
			if (i != size - 1)
				tags += ", ";
		}
		System.out.printf("tags: %s\n", tags);
		((TextView) findViewById(R.id.tags)).setText("Tags: " + tags);

		ImageView image = null;

		for (Media m : thisEvent.getMedia()) {
			image = new ImageView(this);
			try {
				image.setImageDrawable(drawableFromUrl(m.getUrl()));
			} catch (IOException e1) {
				// TODO Auto-generated catch block
				e1.printStackTrace();
			}
			((LinearLayout) findViewById(R.id.gallery)).addView(image);

		}

	}

	@SuppressWarnings("deprecation")
	public static Drawable drawableFromUrl(String url) throws IOException {
		Bitmap x;
		x = BitmapFactory.decodeFile(url);
		return new BitmapDrawable(x);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
