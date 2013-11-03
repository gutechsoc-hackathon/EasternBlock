package com.guts.hackathon;

import android.R.integer;
import android.R.menu;
import android.annotation.SuppressLint;
import android.app.ActionBar;
import android.app.ActionBar.Tab;
import android.app.Activity;
import android.app.Fragment;
import android.app.FragmentTransaction;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.DisplayMetrics;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.ScrollView;


@SuppressLint("NewApi")
public class MainActivity extends Activity {

	private DisplayMetrics metrics;
	private boolean logedIn = false;
	private Menu menu;
	private static Activity mainActivity;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		mainActivity = this;
		metrics = this.getResources().getDisplayMetrics();
		ActionBar aBar = getActionBar();
		aBar.setNavigationMode(ActionBar.NAVIGATION_MODE_TABS);
		aBar.setDisplayShowTitleEnabled(false);
		//displaying custom ActionBar
		View mActionBarView = getLayoutInflater().inflate(R.layout.actionbar_c, null);

		aBar.setCustomView(mActionBarView);
		aBar.setDisplayOptions(ActionBar.DISPLAY_SHOW_CUSTOM | ActionBar.DISPLAY_SHOW_HOME);
		View homeIcon = findViewById(android.R.id.home);
		((View) homeIcon.getParent()).setVisibility(View.GONE);
		Tab eventsTab = aBar.newTab()
				.setText("Events")
				.setTabListener(new TabListener<EventsFragment>(
						this, "events", EventsFragment.class));


		Tab questionsTab = aBar.newTab()
				.setText("Questions")
				.setTabListener(new TabListener<QuestionsFragment>(
						this, "questions", QuestionsFragment.class));

		aBar.addTab(eventsTab);
		aBar.addTab(questionsTab);
		
		SharedPreferences pref = getSharedPreferences("REMEMBER", Context.MODE_PRIVATE);
		if (pref.contains("SESSION")) {
			ThisUser.session = pref.getString("SESSION", "");
			ThisUser.name = pref.getString("NAME", "");
		}

		//Set actionbar button positioning

		setContentView(R.layout.activity_main);
	}

	static public void updateSizes() {
		/*	FrameLayout list = (FrameLayout) mainActivity.findViewById(R.id.fragment_placeholder);
		list.setLayoutParams(new LinearLayout.LayoutParams(155,155));
		for (int i=0; i < list.getChildCount(); i++) {
			list.getChildAt(i).setLayoutParams(new LinearLayout.LayoutParams(155,155));
		} */
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
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

	/*    @SuppressLint("NewApi")
    private class PositioningLC implements OnLayoutChangeListener {

            @Override
            public void onLayoutChange(View v, int left, int top, int right,
                            int bottom, int oldLeft, int oldTop, int oldRight, int oldBottom) {
                	Log.d("LayoutTest","left="+left+", top="+top+", right="+right+", bottom="+bottom);
                    if (left == 0 && top == 0 && right == 0 && bottom == 0) {
                            return;
                    }
                    ImageView leftmostButton = (ImageView) findViewById(R.id.actBarButtonMap);
                    ImageView events = (ImageView) findViewById(R.id.actBarButtonEvent);
                    ImageView questions = (ImageView) findViewById(R.id.actBarButtonQuestion);
                    RelativeLayout.LayoutParams lParams = (LayoutParams) leftmostButton.getLayoutParams();
                    lParams.rightMargin = (metrics.widthPixels/2) - ((events.getWidth()+questions.getWidth())/2);
                    leftmostButton.setLayoutParams(lParams);
            }

    } */

	@Override
	public boolean onMenuItemSelected(int id, MenuItem item) {
		switch (item.getItemId()) {
		case R.id.post_event:
			Intent postIntent = new Intent(getBaseContext(), PostEventActivity.class);
			startActivity(postIntent);
			return true;
		case R.id.ask_question:
			Intent askIntent = new Intent(getBaseContext(), QuestionFormActivity.class);
			startActivity(askIntent);
			return true;
		case R.id.log_in:
			Intent i = new Intent(getBaseContext(), LoginActivity.class);
			startActivity(i);
			return true;
		case R.id.log_out:
			ThisUser.name = "";
			ThisUser.session = "";
			SharedPreferences pref = getSharedPreferences("REMEMBER", Context.MODE_PRIVATE);
			pref.edit().clear().commit();
			return true;
		case R.id.settings:
			return true;
			// we don't have any other menu items
		}
		return super.onMenuItemSelected(id, item);
	}

	private void hideOption(int id)	{
		MenuItem item = menu.findItem(id);
		item.setVisible(false);
	}

	private void showOption(int id)	{
		MenuItem item = menu.findItem(id);
		item.setVisible(true);
	}

	public static class TabListener<T extends Fragment> implements ActionBar.TabListener {
		private Fragment mFragment;
		private final Activity mActivity;
		private final String mTag;
		private final Class<T> mClass;

		/** Constructor used each time a new tab is created.
		 * @param activity  The host Activity, used to instantiate the fragment
		 * @param tag  The identifier tag for the fragment
		 * @param clz  The fragment's Class, used to instantiate the fragment
		 */
		public TabListener(Activity activity, String tag, Class<T> clz) {
			mActivity = activity;
			mTag = tag;
			mClass = clz;
		}

		/* The following are each of the ActionBar.TabListener callbacks */

		@Override
		public void onTabSelected(Tab tab, FragmentTransaction ft) {
			// Check if the fragment is already initialized
			if (mFragment == null) {
				// If not, instantiate and add it to the activity
				mFragment = Fragment.instantiate(mActivity, mClass.getName());
				ft.add(android.R.id.content, mFragment, mTag);
			} else {
				// If it exists, simply attach it in order to show it
				ft.attach(mFragment);
			}
		}

		@Override
		public void onTabUnselected(Tab tab, FragmentTransaction ft) {
			if (mFragment != null) {
				// Detach the fragment, because another one is being attached
				ft.detach(mFragment);
			}
		}

		@Override
		public void onTabReselected(Tab tab, FragmentTransaction ft) {
			// User selected the already selected tab. Usually do nothing.
		}
	}

	public void openPostEvent(View view) {
		Intent intent = new Intent(this, PostEventActivity.class);
		startActivity(intent);
	}

	public void showMap(View view) {
		Intent intent = new Intent(this, MapActivity.class);
		startActivity(intent);
	}
}
