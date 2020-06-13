package com.magenest.criminalintent;

import android.support.v4.app.Fragment;

/**
 * Created by root on 17/09/2015.
 */
public class CrimeListActivity extends SingleFragmentActivity {
    @Override
    protected Fragment createFragment() {
        return new CrimeListFragment();
    }
}
