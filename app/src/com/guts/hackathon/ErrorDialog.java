package com.guts.hackathon;

import android.app.AlertDialog;
import android.content.Context;

public class ErrorDialog {

    public static void show(Context context, String errorMsg) {
        AlertDialog.Builder builder = new AlertDialog.Builder(context);
        builder.setCancelable(false);
        builder.setNeutralButton("OK", null);
        builder.setMessage(errorMsg).setTitle(R.string.error_title);
        AlertDialog dialog = builder.create();
        dialog.show();
    }
}
