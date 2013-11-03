package com.guts.hackathon;

import java.util.ArrayList;
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
import com.google.android.gms.maps.GoogleMap.OnInfoWindowClickListener;
import com.google.android.gms.maps.MapFragment;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;

@SuppressLint("NewApi")
public class MapActivity extends Activity {
	
	private GoogleMap map;
	private HashMap<String, LocationItem> markers = new HashMap<String, LocationItem>();
	private boolean myLocationCentered = false;
	private long lastPostLocationTime = System.currentTimeMillis();
	private PostToServerTask task;
	private Location lastLocation;
	private ArrayList<Event> events;
	private ArrayList<Question> questions;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_map);

	    map = ((MapFragment) getFragmentManager().findFragmentById(R.id.map))
	            .getMap();
	    
	    map.setOnInfoWindowClickListener(new OnInfoWindowClickListener() {

			@Override
			public void onInfoWindowClick(Marker arg0) {
				String id = arg0.getId();
				LocationItem item = markers.get(id);
				System.out.println(id);
				System.out.println(item);
				
				if (item.obj instanceof Event) {
					Event e = (Event)item.obj;
					System.out.println(e.getDescription());
				} else if (item.obj instanceof Question) {
					Question q = (Question)item.obj;
					System.out.println(q.getQuestion());
				}
				
			}
	    });

		for (Event x: DataAccess.getEvents()) {
				System.out.println(x.getLocation().getLatitude() + " " +x.getLocation().getLongitude());
		}	    
	    
		
		for (Event x: DataAccess.getEvents()) {
			setMarker(x);
		}

		for (Question x: DataAccess.getQuestions()) {
			setMarker(x);	
		}
		
  	    
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
	
	private void setMarker(Object obj) {
		MarkerOptions options = new MarkerOptions();
		
		if (obj instanceof Event) {
			Event e = (Event)obj;
					
			options.title(e.getUser_name());
			options.position(new LatLng(e.getLocation().getLatitude(), e.getLocation().getLongitude()));
			options.snippet(e.getDescription());
			options.icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_AZURE));
								
		} else if (obj instanceof Question) {
			Question q = (Question)obj;
			
			options.title(q.getUser_name());
			options.position(new LatLng(q.getLocation().getLatitude(), q.getLocation().getLongitude()));
			options.snippet(q.getQuestion());
		}
		
		
	

		Marker m = map.addMarker(options);
		System.out.println("   " + m.getId());
		markers.put(m.getId(), new LocationItem(obj, options));
		
	}
	

	
	
	private float getCurrentZoomLevel() {
		return map.getCameraPosition().zoom;
	}
	
	private void moveToMarker(String id) {
		MarkerOptions options = markers.get(id).options;
		map.animateCamera(CameraUpdateFactory.newLatLng(options.getPosition()));
	}

	
	private class ResponseCallbackInner implements ResponseCallback {
		@Override
		public void processResponse(String response) {
			
		}		
	}
	
	private class LocationItem {
		public Object obj;
		public MarkerOptions options;
		
		public LocationItem(Object obj, MarkerOptions options) {
			this.obj = obj;
			this.options = options;
		}
	}
}
