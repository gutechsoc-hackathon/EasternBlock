package com.guts.hackathon;

import java.lang.reflect.Type;
import java.util.ArrayList;

import android.app.Activity;
import android.content.Context;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.google.gson.reflect.TypeToken;

public class EventsAdapter extends BaseAdapter implements ResponseCallback{

	private Activity activity;
	private static LayoutInflater inflater=null;
	private ArrayList<Event> events;

	public EventsAdapter(Activity a) {
		events = new ArrayList<Event>();
		activity = a;
		inflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		refresh();
	}

	public void refresh() {
		// Create a new HttpClient and Post Header
		PostToServerTask go = new PostToServerTask();
		go.setDone(this);
		go.execute("r","events/list");
	}
	
	@Override
	public void processResponse(String response) {
		Gson gson = new GsonBuilder().setDateFormat("yyyy-MM-dd HH:mm:ss").create();
		
		Type listType = new TypeToken<ArrayList<Event>>() {}.getType();
		//TODO: check response before attempting deserialization
		response = response.substring(response.indexOf("["),response.lastIndexOf("]")+1);
		events = gson.fromJson(response, listType);
		notifyDataSetChanged();
		DataAccess.updateEvents(events);
	}

	@Override
	public int getCount() {
		return events.size();
	}

	@Override
	public Object getItem(int position) {
		return events.get(position);
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub
		return 0;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View elem = convertView;
		if(convertView==null) {
			elem = inflater.inflate(R.layout.event, null);
		}
		TextView locName = (TextView) elem.findViewById(R.id.eventLocationName);
		locName.setText(events.get(position).getLocation().getName());
		TextView description = (TextView) elem.findViewById(R.id.eventDescription);
		description.setText(events.get(position).getDescription());
		ImageView upButton = (ImageView) elem.findViewById(R.id.eventVoteUp);
		upButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				// TODO open event details activity

			}
		});
		return elem;
	}

	public ArrayList<Event> getEvents() {
		return events;
	}

	public void setEvents(ArrayList<Event> events) {
		this.events = events;
	}

}
