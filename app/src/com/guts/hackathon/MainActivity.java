package com.guts.hackathon;

import android.annotation.SuppressLint;
import android.app.ActionBar;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;

@SuppressLint("NewApi")
public class MainActivity extends Activity {
	
	private boolean logedIn = false;
	private Menu menu;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		ActionBar aBar = getActionBar();
		aBar.setDisplayShowHomeEnabled(false);
		aBar.setDisplayShowTitleEnabled(false);
		aBar.setDisplayUseLogoEnabled(false);
		//displaying custom ActionBar
		View mActionBarView = getLayoutInflater().inflate(R.layout.actionbar_c, null);
		aBar.setCustomView(mActionBarView);
		aBar.setDisplayOptions(ActionBar.DISPLAY_SHOW_CUSTOM);
		setContentView(R.layout.activity_main);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		this.menu = menu;
		getMenuInflater().inflate(R.menu.main, menu);
		
		if(logedIn){
			showOption(R.id.log_out);
			showOption(R.id.profile);
			hideOption(R.id.log_in);
		} else {
			showOption(R.id.log_in);
			hideOption(R.id.profile);
			hideOption(R.id.log_out);
		}
		return true;		
	}
	
	private void hideOption(int id)
	{
	    MenuItem item = menu.findItem(id);
	    item.setVisible(false);
	}

	private void showOption(int id)
	{
	    MenuItem item = menu.findItem(id);
	    item.setVisible(true);
	}

//	private void setOptionTitle(int id, String title)
//	{
//	    MenuItem item = menu.findItem(id);
//	    item.setTitle(title);
//	}

	
	public boolean onMenuItemSelected(int id, MenuItem item) {
		switch (item.getItemId()) {
		case R.id.log_in:
			Intent i = new Intent(getBaseContext(), LoginActivity.class);
			startActivity(i);
			return true;
		case R.id.log_out:
			return true;
		case R.id.settings:
			return true;
			// we don't have any other menu items
		}
		return super.onMenuItemSelected(id, item);
	}
}
