package com.guts.hackathon;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.annotation.TargetApi;
import android.app.Activity;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

/**
 * Activity which displays a login screen to the user, offering registration as
 * well.
 */
public class LoginActivity extends Activity {


	/**
	 * The default email to populate the email field with.
	 */
	public static final String EXTRA_EMAIL = "com.example.android.authenticatordemo.extra.EMAIL";

	/**
	 * Keep track of the login task to ensure we can cancel it if requested.
	 */
	private UserLoginTask mAuthTask = null;

	// Values for email and password at the time of the login attempt.
	private String mEmail;
	private String mPassword;
	private String mEmailRep;
	private String mPasswordRep;
	private String mName;
	private boolean logedIn, reg;

	// UI references.
	private EditText mEmailView;
	private EditText mPasswordView;
	private EditText mEmailViewRep;
	private EditText mPasswordViewRep;
	private EditText mNameView;
	private View mLoginFormView;
	private View mLoginStatusView;
	private TextView mLoginStatusMessageView;
	private Menu menu;
	private Button logIn, signUp, register;
	


	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		setContentView(R.layout.activity_login);

		mNameView = (EditText) findViewById(R.id.name);
		// Set up the login/registration form.
		mEmail = getIntent().getStringExtra(EXTRA_EMAIL);
		mEmailView = (EditText) findViewById(R.id.email);
		mEmailViewRep = (EditText) findViewById(R.id.email_rep);
		mEmailView.setText(mEmail);

		mPasswordView = (EditText) findViewById(R.id.password);
		mPasswordViewRep = (EditText) findViewById(R.id.password_rep);
		mPasswordView
				.setOnEditorActionListener(new TextView.OnEditorActionListener() {
					@Override
					public boolean onEditorAction(TextView textView, int id,
							KeyEvent keyEvent) {
						if (id == R.id.login || id == EditorInfo.IME_NULL) {
							attemptLogin(false);
							return true;
						}
						return false;
					}
				});

		mLoginFormView = findViewById(R.id.login_form);
		mLoginStatusView = findViewById(R.id.login_status);
		mLoginStatusMessageView = (TextView) findViewById(R.id.login_status_message);

		logIn = (Button) findViewById(R.id.log_in_button);
		logIn.setOnClickListener(
				new View.OnClickListener() {
					@Override
					public void onClick(View view) {
						attemptLogin(false);
					}
				});
		
		signUp = (Button) findViewById(R.id.sign_up_button);
		register = (Button) findViewById(R.id.register_button);
		
		signUp.setOnClickListener(
				new View.OnClickListener() {
					@Override
					public void onClick(View view) {
						mEmailViewRep.setVisibility(View.VISIBLE);
						mPasswordViewRep.setVisibility(View.VISIBLE);
						mNameView.setVisibility(View.VISIBLE);
						mNameView.requestFocus(View.VISIBLE);
						logIn.setVisibility(View.GONE);
						signUp.setVisibility(View.GONE);
						register.setVisibility(View.VISIBLE);
						setTitle("Registration");
					}
				});
		
		
		
		register.setOnClickListener(
				new View.OnClickListener() {
					@Override
					public void onClick(View view) {
						signUp.setVisibility(View.GONE);
						attemptLogin(true);
					}
				});
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		this.menu = menu;
		super.onCreateOptionsMenu(menu);
		getMenuInflater().inflate(R.menu.login, menu);
		
		if(logedIn){
			showOption(R.id.settings);
			showOption(R.id.action_forgot_password);
			hideOption(R.id.profile);
		} else {
			hideOption(R.id.settings);
			hideOption(R.id.action_forgot_password);
			showOption(R.id.profile);
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

	/**
	 * Attempts to sign in or register the account specified by the login form.
	 * If there are form errors (invalid email, missing fields, etc.), the
	 * errors are presented and no actual login attempt is made.
	 */
	public void attemptLogin(boolean reg) {
		if (mAuthTask != null) {
			return;
		}
		
		this.reg=reg;

		// Reset errors.
		mEmailView.setError(null);
		mPasswordView.setError(null);

		// Store values at the time of the login attempt.
		mEmail = mEmailView.getText().toString();
		mPassword = mPasswordView.getText().toString();
		if(reg){
			mEmailRep = mEmailViewRep.getText().toString();
			mPasswordRep = mPasswordViewRep.getText().toString();
			mName = mNameView.getText().toString();
		}

		boolean cancel = false;
		View focusView = null;
		


		if(reg){
			if (TextUtils.isEmpty(mPasswordRep)) {
				mPasswordViewRep.setError(getString(R.string.error_field_required));
				focusView = mPasswordViewRep;
				cancel = true;
			} else if (!mPassword.equals(mPasswordRep)) {
				System.out.printf("%s %s\n", mPassword, mPasswordRep);
				mPasswordView.setError(getString(R.string.error_pswd_match));
				focusView = mPasswordView;
				cancel = true;
			}
		}
		
		// Check for a valid password.
		if (TextUtils.isEmpty(mPassword)) {
			mPasswordView.setError(getString(R.string.error_field_required));
			focusView = mPasswordView;
			cancel = true;
		} else if (mPassword.length() < 6) {
			mPasswordView.setError(getString(R.string.error_invalid_password));
			focusView = mPasswordView;
			cancel = true;
		} 
		
		if(reg){
			
			if (TextUtils.isEmpty(mEmailRep)) {
				mEmailViewRep.setError(getString(R.string.error_field_required));
				focusView = mEmailViewRep;
				cancel = true;
			} else if (!mEmail.equals(mEmailRep)) {
				mEmailView.setError(getString(R.string.error_email_match));
				focusView = mEmailView;
				cancel = true;
			}
		}

		// Check for a valid email address.
		if (TextUtils.isEmpty(mEmail)) {
			mEmailView.setError(getString(R.string.error_field_required));
			focusView = mEmailView;
			cancel = true;
		} else if (!mEmail.contains("@")) {
			mEmailView.setError(getString(R.string.error_invalid_email));
			focusView = mEmailView;
			cancel = true;
		}
		
		if(reg){
			if (TextUtils.isEmpty(mName)) {
				mNameView.setError(getString(R.string.error_field_required));
				focusView = mNameView;
				cancel = true;
			} else if (mName.length() < 3) {
				mNameView.setError(getString(R.string.error_invalid_name));
				focusView = mNameView;
				cancel = true;
			} 
		}

		if (cancel) {
			// There was an error; don't attempt login and focus the first
			// form field with an error.
			focusView.requestFocus();
		} else {
			// Show a progress spinner, and kick off a background task to
			// perform the user login attempt.
			mLoginStatusMessageView.setText(R.string.login_progress_signing_in);
			showProgress(true);
			mAuthTask = new UserLoginTask();
			mAuthTask.execute((Void) null);
		}
	}

	/**
	 * Shows the progress UI and hides the login form.
	 */
	@TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
	private void showProgress(final boolean show) {
		// On Honeycomb MR2 we have the ViewPropertyAnimator APIs, which allow
		// for very easy animations. If available, use these APIs to fade-in
		// the progress spinner.
		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2) {
			int shortAnimTime = getResources().getInteger(
					android.R.integer.config_shortAnimTime);

			mLoginStatusView.setVisibility(View.VISIBLE);
			mLoginStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mLoginStatusView.setVisibility(show ? View.VISIBLE
									: View.GONE);
						}
					});

			mLoginFormView.setVisibility(View.VISIBLE);
			mLoginFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mLoginFormView.setVisibility(show ? View.GONE
									: View.VISIBLE);
						}
					});
		} else {
			// The ViewPropertyAnimator APIs are not available, so simply show
			// and hide the relevant UI components.
			mLoginStatusView.setVisibility(show ? View.VISIBLE : View.GONE);
			mLoginFormView.setVisibility(show ? View.GONE : View.VISIBLE);
		}
	}

	/**
	 * Represents an asynchronous login/registration task used to authenticate
	 * the user.
	 */
	public class UserLoginTask extends AsyncTask<Void, Void, Boolean> {
		@Override
		protected Boolean doInBackground(Void... params) {
			// TODO: attempt authentication against a network service.
		    HttpClient httpclient = new DefaultHttpClient();
		    HttpPost httppost = new HttpPost("http://shacron.twilightparadox.com/hackathon/index.php");
		    

			InputStream inputStream = null;
			String result = "";
			JSONObject jObject = null;
			JSONObject data= null;
			
		    try {
		        // Add your data
		        List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(2);
		        
		        if(reg){
		        	nameValuePairs.add(new BasicNameValuePair("name", mName));
		        	nameValuePairs.add(new BasicNameValuePair("r", "user/register"));
		        } else
		        	nameValuePairs.add(new BasicNameValuePair("r", "user/login"));
		        nameValuePairs.add(new BasicNameValuePair("email", mEmail));
		        nameValuePairs.add(new BasicNameValuePair("pass", mPassword));
		        
		        httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

		        // Execute HTTP Post Request
		        HttpResponse response = httpclient.execute(httppost);
		        HttpEntity entity = response.getEntity();
		        
		        inputStream = entity.getContent();
		        BufferedReader reader = new BufferedReader(new InputStreamReader(inputStream, "UTF-8"), 8);
		        StringBuilder sb = new StringBuilder();

		        String line = null;
		        while ((line = reader.readLine()) != null)
		        {
		            sb.append(line + "\n");
		        }
		        inputStream.close();
		        result = sb.toString();
		        
		        
		    } catch (ClientProtocolException e) {
		    	return false;
		        // TODO Auto-generated catch block
		    } catch (IOException e) {
		    	return false;
		        // TODO Auto-generated catch block
		    }
		    
		    
		    try {
				jObject = new JSONObject(result);
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
				return false;
			}
		    String name, session, type; 
		    try {
		    	System.out.println("fetch data");
		    	type = jObject.getString("type");
		    	data = jObject.getJSONObject("data");
		    	if(type.equals("auth_response")){
					session = data.getString("sess_id");
					name = data.getString("name");
		    		return true;
		    	} else if(type.equals("error")){
					String error = data.getString("msg");
					mPasswordView.setError(error);
					return false;
				} else {
					return false;
				}
					
					
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
				return false;
			}


//			try {
//				// Simulate network access.
//				Thread.sleep(2000);
//			} catch (InterruptedException e) {
//				return false;
//			}
//
//			for (String credential : DUMMY_CREDENTIALS) {
//				String[] pieces = credential.split(":");
//				if (pieces[0].equals(mEmail)) {
//					// Account exists, return true if the password matches.
//					return pieces[1].equals(mPassword);
//				}
//			}

			// TODO: register the new account here.
			//return true;
		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mAuthTask = null;
			showProgress(false);

			if (success) {
				finish();
			} else {
				mPasswordView
						.setError(getString(R.string.error_incorrect_password));
				mPasswordView.requestFocus();
			}
		}

		@Override
		protected void onCancelled() {
			mAuthTask = null;
			showProgress(false);
		}
	}
}