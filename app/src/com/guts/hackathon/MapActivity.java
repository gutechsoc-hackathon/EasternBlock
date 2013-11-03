package com.guts.hackathon;

import java.util.HashMap;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.os.Bundle;
import android.view.Menu;

import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapFragment;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MarkerOptions;

@SuppressLint("NewApi")
public class MapActivity extends Activity {
	
	private GoogleMap map;
	private HashMap<String, MarkerOptions> markers = new HashMap<String, MarkerOptions>(); 

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_map);
		
	    map = ((MapFragment) getFragmentManager().findFragmentById(R.id.map))
	            .getMap();
//        super.onCreate(savedInstanceState);
//        setContentView(R.layout.activity_map);
	    
	    setMarker("0", createOptions("Glasgow", 55.8580, -4.2590, "text1"));
	    setMarker("1", createOptions("Glasgow", 0, 0, "text2"));
	    
  	    moveToMarker("0");
	    
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.map, menu);
		return true;
	}
	
	private void setMarker(String id, MarkerOptions options) {
		markers.put(id, options);
		updateMap();
	}
	
	private void removeMarker(String id) {
		markers.remove(id);
		updateMap();
	}
	
	private MarkerOptions getMarker(String id) {
		return markers.get(id);
	}
	
	private void updateMap() {
		map.clear();
		for (String id: markers.keySet()) {
			MarkerOptions options = markers.get(id);
			map.addMarker(options);			
		}
		// other info
	}
	
	private MarkerOptions createOptions(String title, double lat, double lon, String snippetText) {
		MarkerOptions options = new MarkerOptions();
		
		options.position(new LatLng(lat, lon));
		options.title(title);
		options.snippet(snippetText);
		
		return options;
	}
	
	private float getCurrentZoomLevel() {
		return map.getCameraPosition().zoom;
	}
	
	private void moveToMarker(String id) {
		MarkerOptions options = markers.get(id);
		map.animateCamera(CameraUpdateFactory.newLatLng(options.getPosition()));
	}
	
}
