package com.guts.hackathon;

import java.util.ArrayList;

import android.app.Activity;
import android.content.Context;
import android.util.Pair;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;

public class EventsAdapter extends BaseAdapter {
	
	private Activity activity;
	private static LayoutInflater inflater=null;
	private ArrayList<Event> events;
    public EventsAdapter(Activity a) {
    	events = new ArrayList<Event>();
        activity = a;
        inflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
    }

	@Override
	public int getCount() {
		// TODO Auto-generated method stub
		return events.size();
	}

	@Override
	public Object getItem(int position) {
		// TODO Auto-generated method stub
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
        return elem;
	}

}
