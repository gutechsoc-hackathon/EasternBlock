package com.guts.hackathon;

import java.util.ArrayList;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

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
        TextView locName = (TextView) elem.findViewById(R.id.locName);
//        locName.setText(events.get(position).getLocation());
        TextView description = (TextView) elem.findViewById(R.id.description);
        description.setText(events.get(position).getDescription());
        ImageView upButton = (ImageView) elem.findViewById(R.id.vote);
        upButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				
			}
		});
        return elem;
	}

}
