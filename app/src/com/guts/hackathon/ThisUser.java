package com.guts.hackathon;



public class ThisUser {
	private static ThisUser user;
	public static int id = 0;
	public static String name = "", session = "";
	
	private ThisUser() {
		
	}
	
	public static ThisUser getInstance() {
		if (user == null) {
			user = new ThisUser();
		}
		return user;
	}
}
