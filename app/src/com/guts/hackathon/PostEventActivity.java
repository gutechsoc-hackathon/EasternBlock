package com.guts.hackathon;

import java.util.ArrayList;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.drawable.BitmapDrawable;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.Bundle;
import android.provider.MediaStore;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.MultiAutoCompleteTextView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.Iterator;

public class PostEventActivity extends Activity {

    private int CAPTURE_IMAGE = 1;

    protected RadioGroup mRadioGroup;
    protected RadioButton mRadioEvent, mRadioWarning;
    protected MultiAutoCompleteTextView mTagTextView, mLocationTextView;
    protected EditText mDescriptionTextView;
    protected Spinner mSpinner;

    protected ProgressDialog mPdialog;

    @Override
	protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.post_event_form);

        setupSpinner();
        setupTagTextView();
        setupLocationTextView();
        setupSubmit();

        mDescriptionTextView = (EditText) findViewById(R.id.postevent_descText);
        mRadioGroup = (RadioGroup) findViewById(R.id.postevent_radiogroup);
        mRadioEvent = (RadioButton) findViewById(R.id.postevent_radioevent);
        mRadioWarning = (RadioButton) findViewById(R.id.postevent_radiowarning);

        Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        startActivityForResult(takePictureIntent, CAPTURE_IMAGE);
    }

    @SuppressLint("NewApi")
	@Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (resultCode == Activity.RESULT_OK) {
            if (requestCode == CAPTURE_IMAGE) {
                Bundle extras = data.getExtras();
                Bitmap img = (Bitmap) extras.get("data");
                LinearLayout layout = (LinearLayout) findViewById(R.id.postevent_layout);
                layout.setBackground(new BitmapDrawable(getResources(), img));
            }
        } else {
            finish();
        }
    }

    private void setupSpinner() {
        mSpinner = (Spinner) findViewById(R.id.spinner);
        ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(this,
                R.array.expiration_array, android.R.layout.simple_spinner_item);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        mSpinner.setAdapter(adapter);
    }

    private void setupTagTextView() {
        final PostEventActivity thisRef = this;

        mTagTextView = (MultiAutoCompleteTextView) findViewById(R.id.multiAutoCompleteTextView);
        mTagTextView.setTokenizer(new MultiAutoCompleteTextView.CommaTokenizer());
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(thisRef, android.R.layout.simple_list_item_1, new ArrayList<String>());
        mTagTextView.setAdapter(adapter);

        PostToServerTask go = new PostToServerTask();
        go.setDone(new ResponseCallback() {
            public void processResponse(String response) {
                Gson gson = new GsonBuilder().setDateFormat("yyyy-MM-dd HH:mm:ss").create();

                Type listType = new TypeToken<ArrayList<TagPOJO>>() {}.getType();
                //TODO: check response before attempting deserialization
                response = response.substring(response.indexOf("["),response.lastIndexOf("]")+1);
                ArrayList<TagPOJO> tags = gson.fromJson(response, listType);

                ArrayAdapter<TagPOJO> adapter = new ArrayAdapter<TagPOJO>(thisRef, android.R.layout.simple_list_item_1, tags);
                mTagTextView.setAdapter(adapter);
            }
        });
        go.execute("r","tag/list");
    }

    private void setupLocationTextView() {
        final PostEventActivity thisRef = this;

        mLocationTextView = (MultiAutoCompleteTextView) findViewById(R.id.postevent_locationNameView);
        mLocationTextView.setTokenizer(new MultiAutoCompleteTextView.CommaTokenizer());
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(thisRef, android.R.layout.simple_list_item_1, new ArrayList<String>());
        mLocationTextView.setAdapter(adapter);

        PostToServerTask go = new PostToServerTask();
        go.setDone(new ResponseCallback() {
            public void processResponse(String response) {
                Gson gson = new GsonBuilder().setDateFormat("yyyy-MM-dd HH:mm:ss").create();

                Type listType = new TypeToken<ArrayList<Location>>() {}.getType();
                //TODO: check response before attempting deserialization
                response = response.substring(response.indexOf("["),response.lastIndexOf("]")+1);
                ArrayList<Location> locations = gson.fromJson(response, listType);

                ArrayAdapter<Location> adapter = new ArrayAdapter<Location>(thisRef, android.R.layout.simple_list_item_1, locations);
                mLocationTextView.setAdapter(adapter);

                //mPdialog.hide();
            }
        });
        go.execute("r","location/list");
    }

    private void setupSubmit() {
        final PostEventActivity thisActivity = this;
        Button button = (Button) findViewById(R.id.submitButton);
        button.setOnClickListener(new View.OnClickListener() {
            @Override
			public void onClick(View v) {

                mRadioWarning.setError(null);

                Boolean cancel = false;
                if (mRadioGroup.getCheckedRadioButtonId() < 0) {
                    mRadioWarning.setError("");
                    cancel = true;
                }

                if (!cancel) {
                    PostToServerTask go = new PostToServerTask();
                    go.setDone(new ResponseCallback() {
                        public void processResponse(String response) {
                            if (mPdialog != null) mPdialog.hide();
                            finish();
                        }
                    });

                    LocationManager locationManager = (LocationManager) thisActivity.getSystemService(Context.LOCATION_SERVICE);
                    android.location.Location curLoc = locationManager.getLastKnownLocation(LocationManager.NETWORK_PROVIDER);

                    int i = mSpinner.getSelectedItemPosition();
                    Gson gson = new Gson();
                    go.execute("r","events/register",
                            "description", mDescriptionTextView.getText().toString(),
                            "location_id", "1",
                            "type_id", mRadioGroup.getCheckedRadioButtonId() == mRadioEvent.getId() ? "1" : "2",
                            "tags", mTagTextView.getText().toString(),
                            "longitude", Double.toString(curLoc.getLongitude()),
                            "latitude", Double.toString(curLoc.getLatitude()),
                            "expires", Integer.toString(mSpinner.getSelectedItemPosition())
                    );

                    mPdialog = new ProgressDialog(thisActivity);
                    mPdialog.setCancelable(true);
                    mPdialog.setCanceledOnTouchOutside(false);
                    mPdialog.setOnCancelListener(new DialogInterface.OnCancelListener() {
                        @Override
                        public void onCancel(android.content.DialogInterface dialogInterface) {
                            finish();
                        }
                    });
                    mPdialog.setMessage("Posting event ....");
                    mPdialog.show();
                }
            }
        });
    }
}
