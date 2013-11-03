package com.guts.hackathon;

import java.lang.reflect.Type;
import java.util.ArrayList;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.google.gson.reflect.TypeToken;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

public class QuestionsAdapter extends BaseAdapter implements ResponseCallback {
	
	private Activity activity;
	private static LayoutInflater inflater=null;
	private ArrayList<Question> questions;
	
    public QuestionsAdapter(Activity a) {
        activity = a;
        inflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        questions = new ArrayList<Question>();
    }
    
	public void refresh() {
		// Create a new HttpClient and Post Header
		PostToServerTask go = new PostToServerTask();
		go.setDone(this);
		go.execute("r","question/list");
	}
	
	@Override
	public void processResponse(String response) {
		Gson gson = new GsonBuilder().setDateFormat("yyyy-MM-dd HH:mm:ss").create();
		
		Type listType = new TypeToken<ArrayList<Question>>() {}.getType();
		//TODO: check response before attempting deserialization
		response = response.substring(response.indexOf("["),response.lastIndexOf("]")+1);
		questions = gson.fromJson(response, listType);
		notifyDataSetChanged();
	}


	@Override
	public int getCount() {
		// TODO Auto-generated method stub
		return questions.size();
	}

	@Override
	public Object getItem(int position) {
		// TODO Auto-generated method stub
		return questions.get(position);
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
        	elem = inflater.inflate(R.layout.question, null);
        }
        TextView qDesc = (TextView) elem.findViewById(R.id.questionDescription);
        qDesc.setText(questions.get(position).getQuestion());
        return elem;
	}

}
