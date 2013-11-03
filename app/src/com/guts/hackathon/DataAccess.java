package com.guts.hackathon;

import java.util.ArrayList;

public class DataAccess {
	private static ArrayList<Event> events = new ArrayList<Event>();
	private static ArrayList<Question> questions = new ArrayList<Question>();
	
	public static void updateEvents(ArrayList<Event> e) {
		//events.addAll(e);
		events = e;
	}
	
	public static void updateQuestions(ArrayList<Question> q) {
		//questions.addAll(q);
		questions = q;
	}
	
	public static ArrayList<Event> getEvents() {
		return events;
	}
	
	public static ArrayList<Question> getQuestions() {
		return questions;
	}
}
