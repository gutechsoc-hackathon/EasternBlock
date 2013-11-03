package com.guts.hackathon;

import android.R;
import android.annotation.SuppressLint;
import android.app.Fragment;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ListView;

@SuppressLint("NewApi")
public class EventsFragment extends Fragment{
	
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
        Bundle savedInstanceState) {
        // Inflate the layout for this fragment
    	
        View thisFragment = inflater.inflate(R.layout.events_list, container, false);
        
        return thisFragment;
    }
    
    @Override 
    public void onActivityCreated(Bundle savedInstanceState) { 
        super.onActivityCreated(savedInstanceState); 
           
        ListView lView = (ListView) getActivity().findViewById(R.id.eventsList);
        lView.setAdapter(new EventsAdapter(getActivity()));
       
    }  

}
