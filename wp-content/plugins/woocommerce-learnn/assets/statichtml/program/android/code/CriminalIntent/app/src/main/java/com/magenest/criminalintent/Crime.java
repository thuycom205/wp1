package com.magenest.criminalintent;

import java.sql.Timestamp;
import java.util.Date;
import java.util.UUID;

/**
 * Created by root on 14/09/2015.
 */
public class Crime {
    private UUID mId;
    private String mTitle;
    private Date mDate;
    private boolean mSolved;

    public void setId(UUID mId) {
        this.mId = mId;
    }

    public void setTitle(String mTitle) {
        this.mTitle = mTitle;
    }


    public String getTitle() {
        return mTitle;
    }

    public Crime() {
// Generate unique identifier
        mId = UUID.randomUUID();
    }

    public Date getDate() {
        long now = System.currentTimeMillis();
        mDate = new Date(now);
        return mDate;
    }
    public void setDate(Date date) {
        mDate = date;
    }
    public boolean isSolved() {
        return mSolved;
    }
    public void setSolved(boolean solved) {
        mSolved = solved;
    }

    public UUID getId() {
        return mId;
    }

    @Override
    public String toString() {
        return mTitle;
    }
}
