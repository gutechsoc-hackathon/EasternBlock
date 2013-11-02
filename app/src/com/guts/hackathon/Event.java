package com.guts.hackathon;

import java.sql.Date;
import java.util.ArrayList;

public class Event {
	private	double ID;
	private String locName;
	private String description;
	private Date begin, end;
	private double latitude, longitude;
	private ArrayList<String> tags, media;
	
	public Event() {
		
	}
	
	public double getID() {
		return ID;
	}
	public void setID(double iD) {
		ID = iD;
	}
	public String getLocName() {
		return locName;
	}
	public void setLocName(String locName) {
		this.locName = locName;
	}
	public String getDescription() {
		return description;
	}
	public void setDescription(String description) {
		this.description = description;
	}
	public Date getBegin() {
		return begin;
	}
	public void setBegin(Date begin) {
		this.begin = begin;
	}
	public Date getEnd() {
		return end;
	}
	public void setEnd(Date end) {
		this.end = end;
	}
	public double getLatitude() {
		return latitude;
	}
	public void setLatitude(double latitude) {
		this.latitude = latitude;
	}
	public double getLongitude() {
		return longitude;
	}
	public void setLongitude(double longitude) {
		this.longitude = longitude;
	}
	public ArrayList<String> getTags() {
		return tags;
	}
	public void setTags(ArrayList<String> tags) {
		this.tags = tags;
	}
	public ArrayList<String> getMedia() {
		return media;
	}
	public void setMedia(ArrayList<String> media) {
		this.media = media;
	}
}
