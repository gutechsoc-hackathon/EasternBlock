package com.guts.hackathon;

import java.util.HashMap;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
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
	private boolean myLocationCentered = false;
	private long lastPostLocationTime = System.currentTimeMillis();
	private PostToServerTask task;
	private Location lastLocation;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_map);
		
	    map = ((MapFragment) getFragmentManager().findFragmentById(R.id.map))
	            .getMap();
	    
//	    setMarker("1", createOptions("Glasgow", 55.8580, -4.2590, "text1"));
//	    setMarker("2", createOptions("Glasgow", 0, 0, "text2"));
  	    
  	    map.setMyLocationEnabled(true);
  	    
  	    LocationManager locationManager = (LocationManager) this.getSystemService(Context.LOCATION_SERVICE);
  	    LocationListener locationListener = new LocationListener() {
  	    	@Override
  	    	public void onLocationChanged(Location location) {
  	    		if (!myLocationCentered) {
  	    			lastLocation = location;
  	    			map.animateCamera(CameraUpdateFactory.newLatLngZoom(new LatLng(location.getLatitude(), location.getLongitude()), 15));
  	    			myLocationCentered = true;
  	    		}
  	    		if (((System.currentTimeMillis() - lastPostLocationTime)/1000) > 5*60) {
  	    			lastPostLocationTime = System.currentTimeMillis(); 
  	    			task = new PostToServerTask();
  	    			task.setDone(new ResponseCallbackInner());
  	    			task.execute("r","user/track", "lat", Double.toString(lastLocation.getLatitude()), "long", Double.toString(lastLocation.getLongitude()), "sess_id", ThisUser.session);
  	    		}
  	    	}
			public void onStatusChanged(String provider, int status, Bundle extras) {}
			public void onProviderEnabled(String provider) {}
			public void onProviderDisabled(String provider) {}
  	    };
  	    locationManager.requestLocationUpdates(LocationManager.NETWORK_PROVIDER, 0, 0, locationListener);  	    
  	    
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

	
	private class ResponseCallbackInner implements ResponseCallback {
		@Override
		public void processResponse(String response) {
			
		}		
	}
}
